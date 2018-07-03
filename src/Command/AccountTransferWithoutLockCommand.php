<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Process\Process;

class AccountTransferWithoutLockCommand extends Command
{
    protected static $defaultName = 'account:transfer-without-lock';
    protected $params;

    public function __construct(ParameterBagInterface $params)
    {
        parent::__construct();

        $this->params = $params;
    }

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

    protected function transfer(string $from, int $amount, string $to)
    {
        $process = new Process(sprintf('bin/console account:transfer %s %d %s', $from, $amount, $to));
        $process->setTimeout(null);
        $process->setWorkingDirectory($this->params->get('kernel.project_dir'));
        $process->disableOutput();
        $process->start();
    }
}
