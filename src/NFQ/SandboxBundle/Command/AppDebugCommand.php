<?php

namespace NFQ\SandboxBundle\Command;

use NFQ\SandboxBundle\Service\Helicopter;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppDebugCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:debug')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Set and get');
        $helicopter =$this->getContainer()->get('app.helicopter_set_get');
        $output->writeln($helicopter->getEngine());
        $output->writeln('Event listener');
        $helicopter=$this->getContainer()->get('app.helicopter');
        $output->writeln($helicopter->getEngine());
        $output->writeln('Event subscriber');
        $helicopter=$this->getContainer()->get('app.helicopter_sub');
        $output->writeln($helicopter->getEngine());
    }

}
