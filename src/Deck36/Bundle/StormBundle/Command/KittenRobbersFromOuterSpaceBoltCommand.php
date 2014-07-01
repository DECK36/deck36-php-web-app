<?php 

namespace Deck36\Bundle\StormBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class KittenRobbersFromOuterSpaceBoltCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('storm:bolt:KittenRobbers')
            ->setDescription('Start Storm Bolt for the Plan9 Kitten Robbers from Outer Space Business Logic')                        
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bolt = $this->getContainer()->get("deck36_storm.command.kitten_robbers_bolt");
        $bolt->run();     
    }
}




















