<?php

namespace App\Command;

use App\Entity\Account;
use App\Repository\AccountRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AccountTransferCommand extends Command
{
    protected static $defaultName = 'account:transfer';
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Transfer money from an account to another account')
            ->addArgument('from', InputArgument::REQUIRED, 'From account')
            ->addArgument('amount', InputArgument::REQUIRED, 'Amount of money')
            ->addArgument('to', InputArgument::REQUIRED, 'To account')
            ->addOption('lock', null, InputOption::VALUE_OPTIONAL, 'Lock option', LockMode::NONE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $from = $input->getArgument('from');
        $amount = $input->getArgument('amount');
        $to = $input->getArgument('to');
        $lock = $input->getOption('lock');

        if (!is_numeric($amount)) {
            $io->error(sprintf('Amount is not a number: %d', $amount));
            return;
        }

        if (!in_array($lock, [LockMode::NONE, LockMode::PESSIMISTIC_READ, LockMode::PESSIMISTIC_WRITE])) {
            $io->error(sprintf('Invalid lock: %d', $lock));
            return;
        }

        /** @var AccountRepository $repository */
        $repository = $this->entityManager->getRepository(Account::class);

        $this->entityManager->beginTransaction();
        try {
            $fromAccount = $repository->findOneByNameWithLock($from, $lock);
            if (!$fromAccount) {
                $io->error(sprintf('There is no account by name: %s', $from));
                return;
            }

            $toAccount = $repository->findOneByNameWithLock($to, $lock);
            if (!$toAccount) {
                $io->error(sprintf('There is no account by name: %s', $to));
                return;
            }

            if ($fromAccount->getBalance() < (int) $amount) {
                $io->error(sprintf('There are not enough money to transfer %d from %s: %d', $fromAccount->getBalance(), $fromAccount->getName(), $amount));
                return;
            }

            $io->note(sprintf('Before transfering %d from %s to %s: %s have %d, %s have %d', $amount, $fromAccount->getName(), $toAccount->getName(), $fromAccount->getName(), $fromAccount->getBalance(), $toAccount->getName(), $toAccount->getBalance()));
            $io->note(sprintf('Transfering amount %d from %s to %s...', $amount, $fromAccount->getName(), $toAccount->getName()));

            $fromAccount->setBalance($fromAccount->getBalance() - $amount);
            $toAccount->setBalance($toAccount->getBalance() + $amount);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch(\Exception $e) {
            $this->entityManager->rollback();
            $io->error('Sorry, can not transfer money. Please try again!');
        } finally {
            $io->note(sprintf('After transfering %d from %s to %s: %s have %d, %s have %d', $amount, $fromAccount->getName(), $toAccount->getName(), $fromAccount->getName(), $fromAccount->getBalance(), $toAccount->getName(), $toAccount->getBalance()));

            $io->success('Money transfered!');
        }
    }
}
