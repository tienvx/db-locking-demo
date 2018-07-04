<?php

namespace App\Command;

use Doctrine\DBAL\LockMode;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AccountTransferWithPessimisticReadLockCommand extends TransferWithLockCommand
{
    protected static $defaultName = 'account:transfer-with-pessimistic-read-lock';

    protected function configure()
    {
        $this
            ->setDescription('Transfer money at the same time, with pessimistic read lock')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->transfer('Jack', 1, 'Anne', LockMode::PESSIMISTIC_READ);
        $this->transfer('Frank', 1, 'Anne', LockMode::PESSIMISTIC_READ);

        $io = new SymfonyStyle($input, $output);
        $io->success('Finish transfering money with pessimistic read lock!');
    }
}
