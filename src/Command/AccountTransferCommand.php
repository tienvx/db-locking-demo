<?php

namespace App\Command;

use App\Entity\Account;
use App\Repository\AccountRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\PessimisticLockException;
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
            ->addOption('lock', null, InputOption::VALUE_OPTIONAL, 'Lock option')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $from = $input->getArgument('from');
        $amount = $input->getArgument('amount');
        $to = $input->getArgument('to');

        /** @var AccountRepository $repository */
        $repository = $this->entityManager->getRepository(Account::class);

        $fromAccount = $repository->findOneByName($from);
        if (!$fromAccount) {
            $io->error(sprintf('There is no account by name: %s', $from));
            return;
        }

        $toAccount = $repository->findOneByName($to);
        if (!$toAccount) {
            $io->error(sprintf('There is no account by name: %s', $to));
            return;
        }

        if (!is_numeric($amount)) {
            $io->error(sprintf('Amount is not a number: %d', $amount));
            return;
        }

        if ($fromAccount->getBalance() < (int) $amount) {
            $io->error(sprintf('There are not enough money to transfer %d from %s: %d', $fromAccount->getBalance(), $fromAccount->getName(), $amount));
            return;
        }

        $io->note(sprintf('Before transfering %d from %s to %s: %s have %d, %s have %d', $amount, $fromAccount->getName(), $toAccount->getName(), $fromAccount->getName(), $fromAccount->getBalance(), $toAccount->getName(), $toAccount->getBalance()));
        $io->note(sprintf('Transfering amount %d from %s to %s...', $amount, $fromAccount->getName(), $toAccount->getName()));

        $lock = $input->getOption('lock');
        if ($lock === LockMode::OPTIMISTIC) {
            try {
                $this->entityManager->lock($fromAccount, $lock, $fromAccount->getVersion());
                $this->entityManager->lock($toAccount, $lock, $toAccount->getVersion());

                $fromAccount->setBalance($fromAccount->getBalance() - $amount);
                $toAccount->setBalance($toAccount->getBalance() + $amount);
                $this->entityManager->flush();
            } catch(OptimisticLockException $e) {
                $io->error('Sorry, but account has been changed before transfering. Please try again!');
            }
        } elseif ($lock === LockMode::PESSIMISTIC_WRITE || $lock === LockMode::PESSIMISTIC_READ) {
            try {
                $this->entityManager->lock($fromAccount, $lock);
                $this->entityManager->lock($toAccount, $lock);

                $fromAccount->setBalance($fromAccount->getBalance() - $amount);
                $toAccount->setBalance($toAccount->getBalance() + $amount);
                $this->entityManager->flush();
            } catch(PessimisticLockException $e) {
                $io->error('Sorry, but account has been changed before transfering. Please try again!');
            }
        } else {
            $fromAccount->setBalance($fromAccount->getBalance() - $amount);
            $toAccount->setBalance($toAccount->getBalance() + $amount);
            $this->entityManager->flush();
        }

        $io->note(sprintf('After transfering %d from %s to %s: %s have %d, %s have %d', $amount, $fromAccount->getName(), $toAccount->getName(), $fromAccount->getName(), $fromAccount->getBalance(), $toAccount->getName(), $toAccount->getBalance()));

        $io->success('Money transfered!');
    }
}
