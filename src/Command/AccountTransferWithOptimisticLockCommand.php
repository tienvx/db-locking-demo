<?php

namespace App\Command;

use Doctrine\DBAL\LockMode;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AccountTransferWithOptimisticLockCommand extends TransferWithLockCommand
{
    protected static $defaultName = 'account:transfer-with-optimistic-lock';

    protected function configure()
    {
        $this
            ->setDescription('Transfer money at the same time, with optimistic lock')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->transfer('Jack', 1, 'Anne', LockMode::OPTIMISTIC);
        $this->transfer('Frank', 1, 'Anne', LockMode::OPTIMISTIC);

        $io = new SymfonyStyle($input, $output);
        $io->success('Finish transfering money with optimistic lock!');
    }
}
