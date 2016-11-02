<?php

namespace NFQ\SandboxBundle\Command;

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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $submarine = $this->getContainer()->get('app.submarine');

        $output->writeln("Pagaminimo metai: " . $submarine->getBuilt());
        $output->writeln("Kaina: " . $submarine->getCost());
        $output->writeln("Maksimalus greitis: " . $submarine->getSpeed());
        $output->writeln("Tipas: " . $submarine->getType());
        $output->writeln("Ilgis: " . $submarine->getLength());
    }

}
