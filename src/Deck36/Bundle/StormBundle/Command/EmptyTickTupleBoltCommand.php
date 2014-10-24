<?php

namespace Deck36\Bundle\StormBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EmptyTickTupleBoltCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('storm:bolt:EmptyTickTuple')
            ->setDescription('Start Empty Tick Tuple Storm Bolt');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bolt = $this->getContainer()->get("deck36_storm.command.empty_tick_tuple_bolt");
        $bolt->run();
    }
}




















