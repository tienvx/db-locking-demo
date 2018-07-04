<?php

namespace App\Command;

use App\Entity\Account;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AccountDebugCommand extends Command
{
    protected static $defaultName = 'account:debug';
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Show information about all accounts')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        /** @var AccountRepository $repository */
        $repository = $this->entityManager->getRepository(Account::class);

        $this->debug($repository, 'Jack', $io);
        $this->debug($repository, 'Frank', $io);
        $this->debug($repository, 'Anne', $io);

    }

    protected function debug(AccountRepository $repository, string $name, SymfonyStyle $io)
    {
        $account = $repository->findOneByNameWithLock($name);
        $io->note(sprintf('%s have %d', $account->getName(), $account->getBalance()));
    }
}
