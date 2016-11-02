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


        $boeing = $this->getContainer()->get('app.boeing');
        $output->writeln("Color: ".$boeing->getColor()." <-- Pakeista su event listener");
        $output->writeln("Length: ".$boeing->getLength()." meters   <-- Pakeista su event subscriber");
        $output->writeln("Power: ".$boeing->getPower()." horse power");
        $output->writeln("Year of first flight: ".$boeing->getYearOfRelease());
        $output->writeln('Done.');
    }

}
