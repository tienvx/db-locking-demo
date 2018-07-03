<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AccountTransferWithoutLockCommand extends TransferWithLockCommand
{
    protected static $defaultName = 'account:transfer-without-lock';

    protected function configure()
    {
        $this
            ->setDescription('Transfer money at the same time, without lock')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->transfer('Jack', 1, 'Anne');
        $this->transfer('Frank', 1, 'Anne');

        $io = new SymfonyStyle($input, $output);
        $io->success('Finish transfering money without lock!');
    }
}
