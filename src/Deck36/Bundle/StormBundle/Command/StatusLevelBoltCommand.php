<?php

namespace Deck36\Bundle\StormBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StatusLevelBoltCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('storm:bolt:StatusLevel')
            ->setDescription('Start Storm bolt for the Plan9 Status Level Business Logic');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bolt = $this->getContainer()->get("deck36_storm.command.status_level_bolt");
        $bolt->run();
    }
}




















