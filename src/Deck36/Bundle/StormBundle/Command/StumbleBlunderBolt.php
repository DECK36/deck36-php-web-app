<?php 

namespace Deck36\Bundle\StormBundle\Command;
 
use Deck36\Bundle\Plan9Bundle\Entity\Badge;
use Symfony\Component\DependencyInjection\Container;

require_once('storm.php');

class StumbleBlunderBolt extends BasicBolt
{

	private $container;
    private $userManager;
    private $stumbleCounter;
    private $stumbleBlunderTimeWindow;
    private $stumbleBlunderMaxTolerance;

    private $stumbleBlunderBadgeName;
    private $stumbleBlunderBadgeText;
    private $stumbleBlunderBadgeSize;
    private $stumbleBlunderBadgeColor;
    private $stumbleBlunderBadgeEffect;


	public function __construct(Container $container, 
                                $stumbleBlunderTimeWindow, 
                                $stumbleBlunderMaxTolerance,
                                $stumbleBlunderBadgeName,
                                $stumbleBlunderBadgeText,
                                $stumbleBlunderBadgeSize,
                                $stumbleBlunderBadgeColor,
                                $stumbleBlunderBadgeEffect
                                ) {
        parent::__construct();
		$this->container = $container;        
        $this->userManager = $container->get('fos_user.user_manager'); 

        $this->stumbleCounter = array();
        $this->stumbleBlunderTimeWindow = $stumbleBlunderTimeWindow;
        $this->stumbleBlunderMaxTolerance = $stumbleBlunderMaxTolerance;

        $this->stumbleBlunderBadgeName = $stumbleBlunderBadgeName;
        $this->stumbleBlunderBadgeText = $stumbleBlunderBadgeText;
        $this->stumbleBlunderBadgeSize = $stumbleBlunderBadgeSize;
        $this->stumbleBlunderBadgeColor = $stumbleBlunderBadgeColor;
        $this->stumbleBlunderBadgeEffect = $stumbleBlunderBadgeEffect;
	}


    public function process(Tuple $tuple)
    {

        $object = $tuple->values[0];
        $type = $object['type'];        

        // we need a cbt message        
        if ($type == 'cbt') {
            $result = $object['cbt']['solved'];
            $user = $object['user']['user_id'];

            // reset or increment failure counter
            if ($result == 'true') {
                
                $this->stumbleCounter[$user] = 0;
                return;

            } else {
                
                if (array_key_exists($user, $this->stumbleCounter)) {
                   $this->stumbleCounter[$user] = $this->stumbleCounter[$user] + 1;
                } else {
                   $this->stumbleCounter[$user] = 1; 
                }
            } // if success
            
            // if the maximum level of tolerated failures is reached, emit 
            // the badge and nullify the users points
            if ($this->stumbleCounter[$user] >= $this->stumbleBlunderMaxTolerance) {
                // build a 'badge' 'object' 
                $date = new \DateTime();
                    
                // persist badge to database
                $userRef = $this->userManager->findUserBy(array('id' => $user));

                $badge = new Badge();
                $badge->setName($this->stumbleBlunderBadgeName);
                $badge->setImage("");
                $badge->setCreatedAt($date);

                $userRef->addBadge($badge);
                $this->userManager->updateUser($userRef);

                // construct badge message     
                $stumbleBlunderBadge = array();
                $stumbleBlunderBadge['user'] = array();
                $stumbleBlunderBadge['user']['user_id'] = $user;                
                $stumbleBlunderBadge['timestamp'] = $date->getTimestamp();

                $stumbleBlunderBadge['type'] = 'badge';
                $stumbleBlunderBadge['version'] = 1;
                
                $stumbleBlunderBadge['badge'] = array();
                $stumbleBlunderBadge['badge']['name'] = $this->stumbleBlunderBadgeName;
                $stumbleBlunderBadge['badge']['text'] = $this->stumbleBlunderBadgeText;
                $stumbleBlunderBadge['badge']['size'] = $this->stumbleBlunderBadgeSize;
                $stumbleBlunderBadge['badge']['color'] = $this->stumbleBlunderBadgeColor;
                $stumbleBlunderBadge['badge']['effect'] = $this->stumbleBlunderBadgeEffect;
                
                $stumbleBlunderBadge['points'] = array();
                $stumbleBlunderBadge['points']['increment'] = 0;            

                $stumbleBlunderBadge['action'] = array();
                $stumbleBlunderBadge['action']['type'] = 'none';
                $stumbleBlunderBadge['action']['amount'] = 0;

                // emit the badge
                $this->emit([$stumbleBlunderBadge]); 

                // update points multiplier in redis
                $redis = $this->container->get("snc_redis.default");
                $redisPointsMultiplierKey = "plan9_pointsMultiplier_" . $user;
                $redis->set($redisPointsMultiplierKey, 0);
                $redis->expire($redisPointsMultiplierKey, $this->stumbleBlunderTimeWindow);   

            } // if maximumStumbleGrace

            $this->sendLog("\n\n\n[PHP] STUMBLEBLUNDER COUNTERS : " . serialize($this->stumbleCounter));

        } // if cbt
        
    } // process
   
}

