<?php

namespace Deck36\Bundle\StormBundle\Command;

use Deck36\Bundle\Plan9Bundle\Entity\Badge;
use Symfony\Component\DependencyInjection\Container;

require_once('storm.php');

class KittenRobbersFromOuterSpaceBolt extends BasicBolt
{

    private $container;
    private $userManager;

    private $kittenRobbersBadgeName;
    private $kittenRobbersBadgeText;
    private $kittenRobbersBadgeSize;
    private $kittenRobbersBadgeColor;
    private $kittenRobbersBadgeEffect;

    public function __construct(
        Container $container,
        $kittenRobbersBadgeName,
        $kittenRobbersBadgeText,
        $kittenRobbersBadgeSize,
        $kittenRobbersBadgeColor,
        $kittenRobbersBadgeEffect
    ) {
        parent::__construct();
        $this->container = $container;
        $this->userManager = $container->get('fos_user.user_manager');

        $this->kittenRobbersBadgeName = $kittenRobbersBadgeName;
        $this->kittenRobbersBadgeText = $kittenRobbersBadgeText;
        $this->kittenRobbersBadgeSize = $kittenRobbersBadgeSize;
        $this->kittenRobbersBadgeColor = $kittenRobbersBadgeColor;
        $this->kittenRobbersBadgeEffect = $kittenRobbersBadgeEffect;
    }


    private function isTickTuple(Tuple $tuple)
    {
        return (($tuple->component === '__system') && ($tuple->stream === '__tick'));
    }


    public function process(Tuple $tuple)
    {

        // check for tick tuple
        if ($this->isTickTuple($tuple)) {


            // get a random active user from redis 
            // Get redis client from Symfony2 service container
            $redis = $this->container->get("snc_redis.default");


            // -- old version start
            // $key_highscore = 'plan9_highscore';

            // we need to get all active users, because redis does not yet have ZRANDMEMEBER
            // $activeUsers = $redis->zrange($key_highscore, 0, -1);
            // -- old version end

            $activeUsers = $redis->keys('user_*');

            $randIdx = 0;
            $randIdx = rand(0, count($activeUsers) - 1);

            $robbedUserStr = $activeUsers[$randIdx];

            $tokens = explode("_", $robbedUserStr);
            $robbedUser = intval($tokens[1]);

            $date = new \DateTime();


            // persist badge to database
            $userRef = $this->userManager->findUserBy(array('id' => $robbedUser));

            $badge = new Badge();
            $badge->setName($this->kittenRobbersBadgeName);
            $badge->setImage("");
            $badge->setCreatedAt($date);

            $userRef->addBadge($badge);
            $this->userManager->updateUser($userRef);

            // construct badge message            
            $kittenRobbersFromOuterSpace = array();
            $kittenRobbersFromOuterSpace['user'] = array();
            $kittenRobbersFromOuterSpace['user']['user_id'] = $robbedUser;

            $kittenRobbersFromOuterSpace['timestamp'] = $date->getTimestamp();

            $kittenRobbersFromOuterSpace['type'] = 'badge';
            $kittenRobbersFromOuterSpace['version'] = 1;

            $kittenRobbersFromOuterSpace['badge'] = array();
            $kittenRobbersFromOuterSpace['badge']['name'] = $this->kittenRobbersBadgeName;
            $kittenRobbersFromOuterSpace['badge']['text'] = $this->kittenRobbersBadgeText;
            $kittenRobbersFromOuterSpace['badge']['size'] = $this->kittenRobbersBadgeSize;
            $kittenRobbersFromOuterSpace['badge']['color'] = $this->kittenRobbersBadgeColor;
            $kittenRobbersFromOuterSpace['badge']['effect'] = $this->kittenRobbersBadgeEffect;

            $kittenRobbersFromOuterSpace['points'] = array();
            $kittenRobbersFromOuterSpace['points']['increment'] = -100;

            $kittenRobbersFromOuterSpace['action'] = array();
            $kittenRobbersFromOuterSpace['action']['type'] = 'unsolve';
            $kittenRobbersFromOuterSpace['action']['amount'] = 1;

            // emit the badge
            $this->emit([$kittenRobbersFromOuterSpace]);

        } else {
            // the KittenRobbers are  not approaching, if wqe don't get a tick tuple...

        }

    }

}

