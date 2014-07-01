<?php

namespace Deck36\Bundle\Plan9Bundle\Command;

use Snc\RedisBundle\Client\Phpredis\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Plan9InitializeCommand extends ContainerAwareCommand
{
    /**
     * @var Client
     */
    private $redisClient;

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('plan9:initialize')
            ->setDescription('Initialize the Plan9 Playground. *WARNING: Deletes all state of the current game!*');
        ;

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->redisClient = $redis = $this->getContainer()->get("snc_redis.default");

        $this->deleteKey('plan9*', true);

        $emptyPixels = array();
        for ($i = 0; $i < $this->getContainer()->getParameter('deck36_plan9.parameter.overview.size.rows'); $i++) {
            for ($j = 0; $j < $this->getContainer()->getParameter('deck36_plan9.parameter.overview.size.cols'); $j++) {
                $emptyPixels[] = array($i, $j);
            }
        }

        // create list of empty pixels based on the configurations.
        $this->createEmptyList($this->getContainer()->getParameter('deck36_plan9.parameter.list_pixel_free'), $emptyPixels);
    }

    /**
     * Creates a empty list to start the game
     *
     * @param string $listName
     *
     * @return bool
     */
    private function createEmptyList($listName, $initialValues = array()) {
        $listName = (string) $listName;
        $this->deleteKey($listName);

        $stringifiedValues = array();
        $storeList = function(&$stringifiedValues) {
            return function ($value) use (&$stringifiedValues) {
                $stringifiedValues[] = json_encode($value);
            };
        };

        if (0 != count($initialValues)) {
            array_walk($initialValues, $storeList($stringifiedValues));
            $this->redisClient->sadd($listName, $stringifiedValues);
        }

        return false;
    }

    /**
     * @param string  $listName
     * @param bool $nameIsPrefix
     *
     * @return void
     */
    private function deleteKey($keyName, $nameIsPrefix = false)
    {
        $keyName = (string) $keyName;

        if (true == $nameIsPrefix) {
            // drop all game keys
            $keys = $this->redisClient->keys($keyName);

            foreach ($keys as $key) {
                $this->redisClient->del($key);
            }
        } else {
            $this->redisClient->del($keyName);
        }
    }
}



