<?php 

namespace Deck36\Bundle\StormBundle\Command;

use Deck36\Bundle\Plan9Bundle\Entity\Badge;
use Symfony\Component\DependencyInjection\Container;

require_once('storm.php');

class HighFiveBolt extends BasicBolt
{

	private $container;
    private $userManager;
    private $highFiveTimeWindow;
    
    private $highFiveBadgeName;
    private $highFiveBadgeText;
    private $highFiveBadgeSize;
    private $highFiveBadgeColor;
    private $highFiveBadgeEffect;
    
	public function __construct(Container $container, 
                                $highFiveTimeWindow,
                                $highFiveBadgeName,
                                $highFiveBadgeText,
                                $highFiveBadgeSize,
                                $highFiveBadgeColor,
                                $highFiveBadgeEffect
                                ) {
        parent::__construct();
		$this->container = $container;
        $this->userManager = $container->get('fos_user.user_manager'); 
        $this->highFiveTimeWindow = $highFiveTimeWindow;

        $this->highFiveBadgeName = $highFiveBadgeName;
        $this->highFiveBadgeText = $highFiveBadgeText;
        $this->highFiveBadgeSize = $highFiveBadgeSize;
        $this->highFiveBadgeColor = $highFiveBadgeColor;
        $this->highFiveBadgeEffect = $highFiveBadgeEffect;
        
	}


    private function extract_user_from_key($keystring) {
        $tokens = explode("_", $keystring);                
        return $tokens[4];
    } 
 

    public function process(Tuple $tuple)
    {

        $object = $tuple->values[0];

        $type = $object['type'];
        
        if ($type != 'cbt') 
            return;
        
        $result = $object['cbt']['solved'];
        
        if ($result != 'true') 
            return;

        $userObj = $object['user'];
        $user = $userObj['user_id'];
        
        $katze = serialize($object['cbt']['entity_coordinate']);
        
        $this->sendLog("\n\n\n[PHP] HIGHFIVE DATA : " . $type . " " . $result . " " . $katze . " " . $user);                

        // Get redis client from Symfony2 service container
        $redis = $this->container->get("snc_redis.default");

        $key_user = 'plan9_badge_highfive_' . $katze . '_' . $user;
        $key_star = 'plan9_badge_highfive_' . $katze . '_*';

        // We use incr to skip argument parsing
        // We expire the key after 5 seconds, should ideally be a config option
        $redis->incr($key_user);
        $redis->expire($key_user, $this->highFiveTimeWindow);

        $katzen = $redis->keys($key_star);

        // If we find multiple keys, multiple users solved a recent CBT using 
        // the same cat. We'll reward that behaviour with a 'HighFive' badge.
        if (count($katzen) > 1) {

            // extract all the users from the keys
            $userGroup = array();

            foreach ($katzen as $value) {
                array_push($userGroup, $this->extract_user_from_key($value));
                $redis->del($value);
            } // foreach    


            // build and emit a 'badge' 'object' 
            // for every user part of the high five
            $date = new \DateTime();
            $createdAt = $date->getTimestamp();

            $highFiveBadge = array();
            $highFiveBadge['katze'] = $katze;
            
            $highFiveBadge['badge'] = array();
            $highFiveBadge['badge']['name'] = $this->highFiveBadgeName;
            $highFiveBadge['badge']['text'] = $this->highFiveBadgeText;
            $highFiveBadge['badge']['size'] = $this->highFiveBadgeSize;
            $highFiveBadge['badge']['color'] = $this->highFiveBadgeColor;
            $highFiveBadge['badge']['effect'] = $this->highFiveBadgeEffect;
            
            $highFiveBadge['points'] = array();
            $highFiveBadge['points']['increment'] = 20;            

            $highFiveBadge['action'] = array();
            $highFiveBadge['action']['type'] = 'none';
            $highFiveBadge['action']['amount'] = 0;

            $highFiveBadge['type'] = 'badge';
            $highFiveBadge['version'] = 1;
            $highFiveBadge['timestamp'] = $createdAt;
            
            foreach ($userGroup as $user) {

                // specify the user 
                $highFiveBadge['user'] = array();
                $highFiveBadge['user']['user_id'] = $user;
                               
                // emit the badge
                $this->emit([$highFiveBadge]);    

                // persist badge to database
                $userRef = $this->userManager->findUserBy(array('id' => $user));

                $badge = new Badge();
                $badge->setName($this->highFiveBadgeName);
                $badge->setImage("high_five_image");
                $badge->setCreatedAt($date);

                $userRef->addBadge($badge);
                $this->userManager->updateUser($userRef);
            
            } // foreach

        } // if 

    } // process
   
} // HighFiveBolt

