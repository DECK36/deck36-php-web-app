<?php 

namespace Deck36\Bundle\StormBundle\Command;
 
use Deck36\Bundle\Plan9Bundle\Entity\Badge;
use Symfony\Component\DependencyInjection\Container;

require_once('storm.php');

class RaiderOfTheKittenRobbersBolt extends BasicBolt
{

	private $container;
    private $userManager;
    private $raiderOfTheRobbersTimeWindow;
    private $theChosenOneTimeWindow;

    private $raiderOfTheRobbersBadgeName;
    private $raiderOfTheRobbersBadgeText;
    private $raiderOfTheRobbersBadgeSize;
    private $raiderOfTheRobbersBadgeColor;
    private $raiderOfTheRobbersBadgeEffect;

	public function __construct(Container $container, 
                                $raiderOfTheRobbersTimeWindow, 
                                $theChosenOneTimeWindow,
                                $raiderOfTheRobbersBadgeName,
                                $raiderOfTheRobbersBadgeText,
                                $raiderOfTheRobbersBadgeSize,
                                $raiderOfTheRobbersBadgeColor,
                                $raiderOfTheRobbersBadgeEffect
                                ) {
        parent::__construct();
		$this->container = $container;     
        $this->userManager = $container->get('fos_user.user_manager'); 

        $this->raiderOfTheRobbersTimeWindow = $raiderOfTheRobbersTimeWindow;
        $this->theChosenOneTimeWindow = $theChosenOneTimeWindow;  

        $this->raiderOfTheRobbersBadgeName = $raiderOfTheRobbersBadgeName;
        $this->raiderOfTheRobbersBadgeText = $raiderOfTheRobbersBadgeText;
        $this->raiderOfTheRobbersBadgeSize = $raiderOfTheRobbersBadgeSize;
        $this->raiderOfTheRobbersBadgeColor = $raiderOfTheRobbersBadgeColor;
        $this->raiderOfTheRobbersBadgeEffect = $raiderOfTheRobbersBadgeEffect;      
	}

    private function processHighFive(Tuple $tuple) {
        $object = $tuple->values[0];

        $user = $object['user']['user_id'];
        
        $redis = $this->container->get("snc_redis.default");
        $key_highfive = 'plan9_badge_raiderkittenrobber_highfive_' . $user;
        
        $redis->incr($key_highfive);
        $redis->expire($key_highfive, $this->raiderOfTheRobbersTimeWindow);
    }

    private function processKittenRobbers(Tuple $tuple) {
        $object = $tuple->values[0];
        
        $user = $object['user']['user_id'];
        
        $redis = $this->container->get("snc_redis.default");
        $key_kittenrobber = 'plan9_badge_raiderkittenrobber_kittenrobber_' . $user;

        $redis->incr($key_kittenrobber);
        $redis->expire($key_kittenrobber, $this->raiderOfTheRobbersTimeWindow);
    }


    public function process(Tuple $tuple)
    {
        $object = $tuple->values[0];

        if (!array_key_exists('badge', $object))
            return;

        $badge = $object['badge']['name'];

        if ($badge == 'HighFive') 
            $this->processHighFive($tuple);
        elseif ($badge == 'KittenRobbersFromOuterSpace')
            $this->processKittenRobbers($tuple);
        else 
            return;

        // Check for the Raider 

        $user = $object['user']['user_id'];

        // Get redis client from Symfony2 service container
        $redis = $this->container->get("snc_redis.default");

        $key_highfive     = 'plan9_badge_raiderkittenrobber_highfive_' . $user;
        $key_kittenrobber = 'plan9_badge_raiderkittenrobber_kittenrobber_' . $user;

        $value_highfive = $redis->get($key_highfive);
        $value_kittenrobber = $redis->get($key_kittenrobber);

        if (empty($value_highfive))
            return;

        if (empty($value_kittenrobber))
            return;

        $redis->del($key_highfive);
        $redis->del($key_kittenrobber);

        // set the chosen one
        $theChosenOneKey = 'plan9_theChosenOne';
        $redis->set($theChosenOneKey, $user);
        $redis->expire($theChosenOneKey, $this->theChosenOneTimeWindow);

        // build a 'badge' 'object' 
        $date = new \DateTime();

        // persist badge to database
        $userRef = $this->userManager->findUserBy(array('id' => $user));

        $badge = new Badge();
        $badge->setName($this->raiderOfTheKittenRobbersBadgeName);
        $badge->setImage("");
        $badge->setCreatedAt($date);

        $userRef->addBadge($badge);
        $this->userManager->updateUser($userRef);

        // construct badge message     
        $raiderOfTheKittenRobbersBadge = array();
        $raiderOfTheKittenRobbersBadge['user'] = array();
        $raiderOfTheKittenRobbersBadge['user']['user_id'] = $user;        
        
        $raiderOfTheKittenRobbersBadge['timestamp'] = $date->getTimestamp();
        
        $raiderOfTheKittenRobbersBadge['type'] = 'badge';
        $raiderOfTheKittenRobbersBadge['version'] = 1;
        
        $raiderOfTheKittenRobbersBadge['badge'] = array();
        $raiderOfTheKittenRobbersBadge['badge']['name'] = $this->raiderOfTheKittenRobbersBadgeName;
        $raiderOfTheKittenRobbersBadge['badge']['text'] = $this->raiderOfTheKittenRobbersBadgeText;
        $raiderOfTheKittenRobbersBadge['badge']['size'] = $this->raiderOfTheKittenRobbersBadgeSize;
        $raiderOfTheKittenRobbersBadge['badge']['color'] = $this->raiderOfTheKittenRobbersBadgeColor;
        $raiderOfTheKittenRobbersBadge['badge']['effect'] = $this->raiderOfTheKittenRobbersBadgeEffect;
        
        $raiderOfTheKittenRobbersBadge['points'] = array();
        $raiderOfTheKittenRobbersBadge['points']['increment'] = 1000;            

        $raiderOfTheKittenRobbersBadge['action'] = array();
        $raiderOfTheKittenRobbersBadge['action']['type'] = 'none';
        $raiderOfTheKittenRobbersBadge['action']['amount'] = 0;

        // emit the badge
        $this->emit([$raiderOfTheKittenRobbersBadge]);    

    }
   
}

