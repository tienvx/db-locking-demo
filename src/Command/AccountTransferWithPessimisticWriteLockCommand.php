<?php

namespace App\Command;

use Doctrine\DBAL\LockMode;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AccountTransferWithPessimisticWriteLockCommand extends TransferWithLockCommand
{
    protected static $defaultName = 'account:transfer-with-pessimistic-write-lock';

    protected function configure()
    {
        $this
            ->setDescription('Transfer money at the same time, with pessimistic write lock')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->transfer('Jack', 1, 'Anne', LockMode::PESSIMISTIC_WRITE);
        $this->transfer('Frank', 1, 'Anne', LockMode::PESSIMISTIC_WRITE);

        $io = new SymfonyStyle($input, $output);
        $io->success('Finish transfering money with pessimistic write lock!');
    }
}
