<?php 

namespace Deck36\Bundle\StormBundle\Command;
 
use Deck36\Bundle\Plan9Bundle\Entity\Badge;
use Symfony\Component\DependencyInjection\Container;

require_once('storm.php');

class PrimeCatBolt extends BasicBolt
{

	private $container;
    private $userManager;
    private $pointsCounter;

    private $primeCatBadgeName;
    private $primeCatBadgeText;
    private $primeCatBadgeSize;
    private $primeCatBadgeColor;
    private $primeCatBadgeEffect;

    private $primeCatTimeWindow = 60;
    
	public function __construct(Container $container, 
                                $primeCatTimeWindow,
                                $primeCatBadgeName,
                                $primeCatBadgeText,
                                $primeCatBadgeSize,
                                $primeCatBadgeColor,
                                $primeCatBadgeEffect
                                ) {
        parent::__construct();
		$this->container = $container;  
        $this->userManager = $container->get('fos_user.user_manager'); 

        $this->pointsCounter = array();  
        $this->primeCatTimeWindow = $primeCatTimeWindow;    

        $this->primeCatBadgeName = $primeCatBadgeName;
        $this->primeCatBadgeText = $primeCatBadgeText;
        $this->primeCatBadgeSize = $primeCatBadgeSize;
        $this->primeCatBadgeColor = $primeCatBadgeColor;
        $this->primeCatBadgeEffect = $primeCatBadgeEffect;

	}


    private function isTickTuple(Tuple $tuple) {
        return (($tuple->component === '__system') && ($tuple->stream === '__tick'));
    }
 

    public function process(Tuple $tuple)
    {

        if ($this->isTickTuple($tuple)) {

            $this->emitPrimeCatAndReset();

        } else {
            
            $this->updateCounters($tuple);        

        }
    }



    private function emitPrimeCatAndReset() {
       
        // log the counter array
        $this->sendLog("\n\n\n[PHP] PRIMECAT POINTSCOUNTER : " . serialize($this->pointsCounter)); 

        // get the PrimeCat user
        $primeCats = array_keys($this->pointsCounter, max($this->pointsCounter));

        // reset array
        $this->pointsCounter = array();

        // construct and emit badges
        // update pointsMultiplier in Redis
        $date = new \DateTime();            
        $timestamp = $date->getTimestamp();

        $redis = $this->container->get("snc_redis.default");

        foreach ($primeCats as $primeCat) {
            
            // persist badge to database
            $userRef = $this->userManager->findUserBy(array('id' => $primeCat));

            $badge = new Badge();
            $badge->setName($this->primeCatBadgeName);
            $badge->setImage("");
            $badge->setCreatedAt($date);

            $userRef->addBadge($badge);
            $this->userManager->updateUser($userRef);

            // construct badge message     
            $primeCatBadge = array();        
            
            $primeCatBadge['user'] = array();
            $primeCatBadge['user']['user_id'] = $primeCat;
            $primeCatBadge['timestamp'] = $timestamp;
            $primeCatBadge['type'] = 'badge';
            $primeCatBadge['version'] = 1;
            
            $primeCatBadge['badge'] = array();
            $primeCatBadge['badge']['name'] = $this->primeCatBadgeName;
            $primeCatBadge['badge']['text'] = $this->primeCatBadgeText;
            $primeCatBadge['badge']['size'] = $this->primeCatBadgeSize;
            $primeCatBadge['badge']['color'] = $this->primeCatBadgeColor;
            $primeCatBadge['badge']['effect'] = $this->primeCatBadgeEffect;
            
            $primeCatBadge['points'] = array();
            $primeCatBadge['points']['increment'] = 0;            

            $primeCatBadge['action'] = array();
            $primeCatBadge['action']['type'] = 'none';
            $primeCatBadge['action']['amount'] = 0;

            // emit badge
            $this->emit([$primeCatBadge]);        

            // set points multiplier in redis
            $redisPointsMultiplierKey = "plan9_pointsMultiplier_" . $primeCat;
            $redis->set($redisPointsMultiplierKey, 2);
            $redis->expire($redisPointsMultiplierKey, $this->primeCatTimeWindow);

        } // foreach

    } // emitPrimeCatAndReset

    
    private function updateCounters(Tuple $tuple) {
        
        $object = $tuple->values[0];

        $type = $object['type'];        
        
        if ($type == 'points') {
            
            $user = $object['user_target']['user_id'];      
            $points = $object['points'];
            $pointsIncrement = $points['increment'];

            if (array_key_exists($user, $this->pointsCounter)) {
                $this->pointsCounter[$user] = $this->pointsCounter[$user] + $pointsIncrement;
            } else {
                $this->pointsCounter[$user] = $pointsIncrement;
            }
        } 
    } // updateCounters
       
}

