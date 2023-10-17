<?php

namespace App\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CustomCommand extends AbstractCommand
{
    protected function configure(): void
    {
        $this
            ->setName('awesome:command')
            ->setDescription('Awesome command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $output->writeln('<info>Custom command created successfully</info>');
            $this->dump("Isn't that awesome?");
            $this->dump("Dump #2");
            $this->dumpVerbose("Dump verbose");
            $this->writeError('oh noes!');
            $this->writeInfo('info');
            $this->writeComment('comment');
            $this->writeQuestion('question');
            return Command::SUCCESS;
        } catch (\Exception $e){
            $output->writeln('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }

    }
}
