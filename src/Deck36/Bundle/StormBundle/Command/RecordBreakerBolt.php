<?php 

namespace Deck36\Bundle\StormBundle\Command;
 
use Deck36\Bundle\Plan9Bundle\Entity\Badge;
use Symfony\Component\DependencyInjection\Container;

require_once('storm.php');

class RecordBreakerBolt extends BasicBolt
{

    // 1. count points 
    // 2. reset counter on cbt fail
    // 3. Persist new record to redis ZSET 
    // 4. If updated record is in top 3 of ZSET, emit the badge


	private $container;
    private $userManager;
    private $currentPointsCounter;

    private $recordBreakerBadgeName;
    private $recordBreakerBadgeText;
    private $recordBreakerBadgeSize;
    private $recordBreakerBadgeColor;
    private $recordBreakerBadgeEffect;
    
	public function __construct(Container $container,
                                $recordBreakerBadgeName,
                                $recordBreakerBadgeText,
                                $recordBreakerBadgeSize,
                                $recordBreakerBadgeColor,
                                $recordBreakerBadgeEffect
                                ) {
        parent::__construct();
		$this->container = $container;    
        $this->userManager = $container->get('fos_user.user_manager'); 

        $this->currentPointsCounter = array();

        $this->recordBreakerBadgeName = $recordBreakerBadgeName;
        $this->recordBreakerBadgeText = $recordBreakerBadgeText;
        $this->recordBreakerBadgeSize = $recordBreakerBadgeSize;
        $this->recordBreakerBadgeColor = $recordBreakerBadgeColor;
        $this->recordBreakerBadgeEffect = $recordBreakerBadgeEffect;
	}


    public function process(Tuple $tuple)
    {

        $object = $tuple->values[0];
        $type = $object['type'];

        if ($type == 'points') 
            $this->updatePointCounters($tuple);

        if ($type == 'cbt') {
            $result = $object['cbt']['solved']; 

            if ($result != 'true')
                $this->newRecordBreaker($tuple);
        }

    } // process



    private function updatePointCounters(Tuple $tuple) {

        $object = $tuple->values[0];
        
        $user = $object['user']['user_id'];        
        $pointsIncrement = $object['points']['increment'];

        if (array_key_exists($user, $this->currentPointsCounter)) {
            $this->currentPointsCounter[$user] = $this->currentPointsCounter[$user] + $pointsIncrement;
        } else {
            $this->currentPointsCounter[$user] = $pointsIncrement;
        }        

    }



    private function newRecordBreaker(Tuple $tuple) {

        $object = $tuple->values[0];
        
        $user = $object['user']['user_id'];
        
        if (!array_key_exists($user, $this->currentPointsCounter))
            return;

        $currentPoints = $this->currentPointsCounter[$user];

        // Get redis client from Symfony2 service container
        $redis = $this->container->get("snc_redis.default");

        $redisKeyRecordBreaker = 'plan9_RecordBreaker';

        $previousRecord = intval($redis->zscore($redisKeyRecordBreaker, $user));

        // check, if we have a new record

        if ($currentPoints <= $previousRecord) {
            // if not, we reset the local counter and return
            $this->currentPointsCounter[$user] = 0;
            return;
        }

        // we have a new record!

        $pointsBetweenRecords = $currentPoints - $previousRecord;

        // update redis 
        $redis->zadd($redisKeyRecordBreaker, $currentPoints, $user);

        // get Top 3 users
        $top3Users = $redis->zrevrange($redisKeyRecordBreaker, 0, 2);

        $top3 = array_search($user, $top3Users);

        $enhancementString = "";
        $bonusPoints = 0;

        if ($top3 > -1) {
            $multiplier = 3 - $top3;
            $bonusPoints = $multiplier * $pointsBetweenRecords;

            $enhancementString = 'TOP ' . ($top3 + 1);
        }


        // build a 'badge' 'object' 
        $date = new \DateTime();
                   
        // persist badge to database
        $userRef = $this->userManager->findUserBy(array('id' => $user));

        $badge = new Badge();
        $badge->setName($this->recordBreakerBadgeName);
        $badge->setImage("");
        $badge->setCreatedAt($date);

        $userRef->addBadge($badge);
        $this->userManager->updateUser($userRef);

        // construct badge message     
        $recordBreakerBadge = array();
        $recordBreakerBadge['user'] = array();
        $recordBreakerBadge['user']['user_id'] = $user;

        $recordBreakerBadge['top'] = $enhancementString;
        $recordBreakerBadge['timestamp'] = $date->getTimestamp();
        $recordBreakerBadge['pbr'] = $pointsBetweenRecords;
            
        $recordBreakerBadge['type'] = 'badge';
        $recordBreakerBadge['version'] = 1;
        
        $recordBreakerBadge['badge'] = array();
        $recordBreakerBadge['badge']['name'] = $this->recordBreakerBadgeName;
        $recordBreakerBadge['badge']['text'] = $this->recordBreakerBadgeText;
        $recordBreakerBadge['badge']['size'] = $this->recordBreakerBadgeSize;
        $recordBreakerBadge['badge']['color'] = $this->recordBreakerBadgeColor;
        $recordBreakerBadge['badge']['effect'] = $this->recordBreakerBadgeEffect;
        
        $recordBreakerBadge['points'] = array();
        $recordBreakerBadge['points']['increment'] = $bonusPoints;            

        $recordBreakerBadge['action'] = array();
        $recordBreakerBadge['action']['type'] = 'none';
        $recordBreakerBadge['action']['amount'] = 0;


        // emit the badge
        $this->emit([$recordBreakerBadge]);    

    }

   
}

