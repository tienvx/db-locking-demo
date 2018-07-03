<?php

namespace App\Command;

use App\Entity\Account;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AccountResetCommand extends Command
{
    protected static $defaultName = 'account:reset';
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Reset data')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var AccountRepository $repository */
        $repository = $this->entityManager->getRepository(Account::class);
        $repository->deleteAll();

        $account1 = new Account();
        $account1->setName('Jack');
        $account1->setBalance(120);
        $this->entityManager->persist($account1);

        $account2 = new Account();
        $account2->setName('Frank');
        $account2->setBalance(45);
        $this->entityManager->persist($account2);

        $account3 = new Account();
        $account3->setName('Anne');
        $account3->setBalance(83);
        $this->entityManager->persist($account3);

        $this->entityManager->flush();

        $io = new SymfonyStyle($input, $output);
        $io->success('Data has been reset!');
    }
}
