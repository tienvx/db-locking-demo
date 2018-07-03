<?php

namespace App\Command;

use Doctrine\DBAL\LockMode;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Process\Process;

abstract class TransferWithLockCommand extends Command
{
    /**
     * @var string
     */
    protected $projectDir;

    /**
     * @var ParameterBagInterface
     */
    protected $params;

    public function __construct(ParameterBagInterface $params)
    {
        parent::__construct();

        $this->params = $params;
        $this->projectDir = $this->params->get('kernel.project_dir');
    }

    protected function transfer(string $from, int $amount, string $to, int $lock = LockMode::NONE)
    {
        $process = new Process(sprintf('bin/console account:transfer %s %d %s --lock=%d', $from, $amount, $to, $lock));
        $process->setTimeout(null);
        $process->setWorkingDirectory($this->projectDir);
        $process->disableOutput();
        $process->start();
    }
}