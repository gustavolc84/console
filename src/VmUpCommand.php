<?php

namespace GestaoTI\Console;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class VmUpCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('vm:up')
            ->setDescription('Start virtual machine')
            ->addArgument('machine', InputArgument::REQUIRED, 'Run the virtual machine on the vagrant.')
            ->addOption('provision', 'p', InputOption::VALUE_NONE, 'Run the provisioners on the box.');
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $machine = $input->getArgument('machine');
        $command = 'vagrant up';
        $path_machine = gestao_path_vms().DIRECTORY_SEPARATOR.$machine;
        
        if ($input->getOption('provision')) {
            $command .= ' --provision';
        }

        if (!is_dir($path_machine)) {
            (new VmInitCommand())->execute($input, $output);
        }

        $process = new Process($command, $path_machine, null, null, null);
        $process->run(function ($type, $line) use ($output, $machine,$path_machine) {
            $output->write($line);
        });
    }
}
