<?php

namespace Deck36\Bundle\StormBundle\Command;

use Deck36\Bundle\Plan9Bundle\Entity\Badge;
use Symfony\Component\DependencyInjection\Container;

require_once('storm.php');

class WheelOfKittenBolt extends BasicBolt
{

    private $container;
    private $userManager;

    private $wheelOfKittenBadgeName;
    private $wheelOfKittenBadgeText;
    private $wheelOfKittenBadgeSize;
    private $wheelOfKittenBadgeColor;
    private $wheelOfKittenBadgeEffect;


    public function __construct(
        Container $container,
        $wheelOfKittenBadgeName,
        $wheelOfKittenBadgeText,
        $wheelOfKittenBadgeSize,
        $wheelOfKittenBadgeColor,
        $wheelOfKittenBadgeEffect
    ) {
        parent::__construct();
        $this->container = $container;
        $this->userManager = $container->get('fos_user.user_manager');

        $this->wheelOfKittenBadgeName = $wheelOfKittenBadgeName;
        $this->wheelOfKittenBadgeText = $wheelOfKittenBadgeText;
        $this->wheelOfKittenBadgeSize = $wheelOfKittenBadgeSize;
        $this->wheelOfKittenBadgeColor = $wheelOfKittenBadgeColor;
        $this->wheelOfKittenBadgeEffect = $wheelOfKittenBadgeEffect;
    }


    public function process(Tuple $tuple)
    {

        $object = $tuple->values[0];

        $this->sendLog("\n\n\n[PHP] WHEEL OF KITTEN INPUT : " . serialize($object));

        $type = $object['type'];

        if ($type != 'points') {
            return;
        }

        $userObj = $object['user_target'];
        $user = $userObj['user_id'];

        // check if we need to construct and emit a badge

        // build a 'badge' 'object'
        $date = new \DateTime();


        $badge = new Badge();
        $badge->setName($this->wheelOfKittenBadgeName);
        $badge->setCreatedAt($date);

        // construct badge message
        $wheelOfKittenBadge = array();

        $wheelOfKittenBadge['timestamp'] = $date->getTimestamp();

        $wheelOfKittenBadge['user'] = $userObj;
        $wheelOfKittenBadge['type'] = 'badge';
        $wheelOfKittenBadge['version'] = 1;

        $wheelOfKittenBadge['badge'] = array();
        $wheelOfKittenBadge['badge']['name'] = $this->wheelOfKittenBadgeName;
        $wheelOfKittenBadge['badge']['text'] = $this->wheelOfKittenBadgeText . ' Wheel of kitten ran for you !';
        $wheelOfKittenBadge['badge']['size'] = $this->wheelOfKittenBadgeSize;
        $wheelOfKittenBadge['badge']['color'] = $this->wheelOfKittenBadgeColor;
        $wheelOfKittenBadge['badge']['effect'] = $this->wheelOfKittenBadgeEffect;

        $wheelOfKittenBadge['points'] = array();
        $wheelOfKittenBadge['points']['increment'] = 0;

        $wheelOfKittenBadge['action'] = array();
        $wheelOfKittenBadge['action']['type'] = 'none';
        $wheelOfKittenBadge['action']['amount'] = 0;

        // emit the badge
        $this->emit([$wheelOfKittenBadge]);

    }

}

