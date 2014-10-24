<?php

namespace Deck36\Bundle\StormBundle\Command;

use Deck36\Bundle\Plan9Bundle\Entity\Badge;
use Symfony\Component\DependencyInjection\Container;

require_once('storm.php');

class DeludedKittenRobbers extends BasicBolt
{

    private $container;
    private $userManager;

    private $deludedKittenRobbersBadgeName;
    private $deludedKittenRobbersBadgeText;
    private $deludedKittenRobbersBadgeSize;
    private $deludedKittenRobbersBadgeColor;
    private $deludedKittenRobbersBadgeEffect;

    public function __construct(
        Container $container,
        $deludedKittenRobbersBadgeName,
        $deludedKittenRobbersBadgeText,
        $deludedKittenRobbersBadgeSize,
        $deludedKittenRobbersBadgeColor,
        $deludedKittenRobbersBadgeEffect
    ) {
        parent::__construct();
        $this->container = $container;
        $this->userManager = $container->get('fos_user.user_manager');

        $this->deludedKittenRobbersBadgeName = $deludedKittenRobbersBadgeName;
        $this->deludedKittenRobbersBadgeText = $deludedKittenRobbersBadgeText;
        $this->deludedKittenRobbersBadgeSize = $deludedKittenRobbersBadgeSize;
        $this->deludedKittenRobbersBadgeColor = $deludedKittenRobbersBadgeColor;
        $this->deludedKittenRobbersBadgeEffect = $deludedKittenRobbersBadgeEffect;
    }


    private function isTickTuple(Tuple $tuple)
    {
        return (($tuple->component === '__system') && ($tuple->stream === '__tick'));
    }


    private function processTickTuple(Tuple $tuple)
    {
        // process tick tuples

        // get a date object to determine the current time
        $date = new \DateTime();

        //// Get a random active user from redis

        // Get redis client from Symfony2 service container
        $redis = $this->container->get("snc_redis.default");

        // Active users do have a session key starting with 'user_'
        // We query all active users from Redis:
        $activeUsers = $redis->keys('user_*');

        // Now we select a random user as the one to be attacked 
        // by the Kitten Robbers:
        $randIdx = 0;
        $randIdx = rand(0, count($activeUsers) - 1);

        $robbedUserStr = $activeUsers[$randIdx];

        $tokens = explode("_", $robbedUserStr);
        $robbedUser = intval($tokens[1]);

        //// persist badge to database
        $userRef = $this->userManager->findUserBy(array('id' => $robbedUser));

        $badge = new Badge();
        $badge->setName($this->deludedKittenRobbersBadgeName);
        $badge->setImage("");
        $badge->setCreatedAt($date);

        $userRef->addBadge($badge);
        $this->userManager->updateUser($userRef);

        //// construct badge message and emit to further Storm components
        $deludedKittenRobbers = array();
        $deludedKittenRobbers['user'] = array();
        $deludedKittenRobbers['user']['user_id'] = $robbedUser;

        $deludedKittenRobbers['timestamp'] = $date->getTimestamp();

        $deludedKittenRobbers['type'] = 'badge';
        $deludedKittenRobbers['version'] = 1;

        $deludedKittenRobbers['badge'] = array();
        $deludedKittenRobbers['badge']['name'] = $this->deludedKittenRobbersBadgeName;
        $deludedKittenRobbers['badge']['text'] = $this->deludedKittenRobbersBadgeText;
        $deludedKittenRobbers['badge']['size'] = $this->deludedKittenRobbersBadgeSize;
        $deludedKittenRobbers['badge']['color'] = $this->deludedKittenRobbersBadgeColor;
        $deludedKittenRobbers['badge']['effect'] = $this->deludedKittenRobbersBadgeEffect;

        $deludedKittenRobbers['points'] = array();
        $deludedKittenRobbers['points']['increment'] = -100;

        $deludedKittenRobbers['action'] = array();
        $deludedKittenRobbers['action']['type'] = 'unsolve';
        $deludedKittenRobbers['action']['amount'] = 1;

        // emit the badge message 
        // we emit the tuple unanchored, because we 
        // don't anchor to ephemeral tick tuples
        $this->emit([$deludedKittenRobbers]);

    }


    private function processTuple(Tuple $tuple)
    {
        // process regular tuples
    }


    public function process(Tuple $tuple)
    {
        // check for tick tuple
        if ($this->isTickTuple($tuple)) {
            $this->processTickTuple($tuple);
        } else {
            $this->processTuple($tuple);
            $this->ack($tuple);
        }
    }

}

