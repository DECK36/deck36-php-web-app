<?php

namespace Deck36\Bundle\StormBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RecordBreakerBoltCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('storm:bolt:RecordBreaker')
            ->setDescription('Start Storm bolt for the Plan9 RecordBreaker Badge Business Logic');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bolt = $this->getContainer()->get("deck36_storm.command.record_breaker_bolt");
        $bolt->run();
    }
}




















