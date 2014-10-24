<?php

namespace Deck36\Bundle\StormBundle\Command;

use Deck36\Bundle\Plan9Bundle\Entity\Badge;
use Symfony\Component\DependencyInjection\Container;

require_once('storm.php');

class StatusLevelBolt extends BasicBolt
{

    private $container;
    private $userManager;
    private $levelConfig;

    private $statusLevelBadgeName;
    private $statusLevelBadgeText;
    private $statusLevelBadgeSize;
    private $statusLevelBadgeColor;
    private $statusLevelBadgeEffect;


    public function __construct(
        Container $container,
        $levelConfig,
        $statusLevelBadgeName,
        $statusLevelBadgeText,
        $statusLevelBadgeSize,
        $statusLevelBadgeColor,
        $statusLevelBadgeEffect
    ) {
        parent::__construct();
        $this->container = $container;
        $this->userManager = $container->get('fos_user.user_manager');
        $this->levelConfig = $levelConfig;

        $this->statusLevelBadgeName = $statusLevelBadgeName;
        $this->statusLevelBadgeText = $statusLevelBadgeText;
        $this->statusLevelBadgeSize = $statusLevelBadgeSize;
        $this->statusLevelBadgeColor = $statusLevelBadgeColor;
        $this->statusLevelBadgeEffect = $statusLevelBadgeEffect;
    }


    private function binary_search($elem, $array)
    {
        $keys = array_keys($array);

        $top = sizeof($array) - 1;
        $bot = 0;

        while ($top >= $bot) {
            $p = (int)floor(($top + $bot) / 2);
            if ($array[$keys[$p]] < $elem) {
                $bot = $p + 1;
            } elseif ($array[$keys[$p]] > $elem) {
                $top = $p - 1;
            } else {
                return $keys[$p];
            }
        }

        return $keys[$top];
    }


    public function process(Tuple $tuple)
    {

        $object = $tuple->values[0];

        $this->sendLog("\n\n\n[PHP] STATUS LEVEL INPUT : " . serialize($object));

        $this->sendLog("\n\n\n[PHP] STATUS LEVEL CONFIG : " . serialize($this->levelConfig));

        $type = $object['type'];

        if ($type != 'points') {
            return;
        }

        $userObj = $object['user_target'];
        $user = $userObj['user_id'];

        // deliberately ignore concurrent updates on points 
        // producing the StatusLevelBadge for a specific level
        // multiple times is idempotent in regard to whether
        // the level has been reached.

        $points = $object['points'];

        // get current amount of points
        $pointsCurrent = $points['total'];

        // get increment
        $pointsIncrement = $points['increment'];


        // update points for user in database
        //$this->userManager->getConnection()->beginTransaction();

        $userRef = $this->userManager->findUserBy(array('id' => $user));
        $userRef->addPoints($pointsIncrement);
        $this->userManager->updateUser($userRef);

        /*  
        try {
            $this->userManager->getConnection()->commit();
        } catch (\Exception $e) {
            $this->userManager->getConnection()->rollback();
            throw new BoltProcessException("could not commit database transaction");
        }
        */

        if ($pointsCurrent < 0) {
            $pointsCurrent = 0;
            $pointsIncrement = 0;
        }

        if ($pointsIncrement < 0) {
            $pointsCurrent = 0;
            $pointsIncrement = 0;
        }

        // get new value of points 
        $pointsPrevious = $pointsCurrent - $pointsIncrement;

        if ($pointsPrevious < 0) {
            $pointsCurrent = 0;
            $pointsIncrement = 0;
            $pointsPrevious = 0;
        }


        // get current status level 
        $statusCurrent = $this->binary_search($pointsCurrent, $this->levelConfig);

        // get updated status level  
        $statusPrevious = $this->binary_search($pointsPrevious, $this->levelConfig);


        // check if we need to construct and emit a badge
        if ($statusCurrent != $statusPrevious) {
            // build a 'badge' 'object' 
            $date = new \DateTime();


            // persist badge to database
            $userRef = $this->userManager->findUserBy(array('id' => $user));

            $badge = new Badge();
            $badge->setName($this->statusLevelBadgeName);
            $badge->setImage($statusCurrent);
            $badge->setCreatedAt($date);

            $userRef->addBadge($badge);
            $this->userManager->updateUser($userRef);

            // construct badge message
            $statusLevelBadge = array();

            $statusLevelBadge['status'] = $statusCurrent;
            $statusLevelBadge['timestamp'] = $date->getTimestamp();

            $statusLevelBadge['user'] = $userObj;
            $statusLevelBadge['type'] = 'badge';
            $statusLevelBadge['version'] = 1;

            $statusLevelBadge['badge'] = array();
            $statusLevelBadge['badge']['name'] = $this->statusLevelBadgeName;
            $statusLevelBadge['badge']['text'] = $this->statusLevelBadgeText . ' You are ' . $statusCurrent . ' !';
            $statusLevelBadge['badge']['size'] = $this->statusLevelBadgeSize;
            $statusLevelBadge['badge']['color'] = $this->statusLevelBadgeColor;
            $statusLevelBadge['badge']['effect'] = $this->statusLevelBadgeEffect;

            $statusLevelBadge['points'] = array();
            $statusLevelBadge['points']['increment'] = 0;

            $statusLevelBadge['action'] = array();
            $statusLevelBadge['action']['type'] = 'none';
            $statusLevelBadge['action']['amount'] = 0;

            // emit the badge
            $this->emit([$statusLevelBadge]);


        }
    }

}

