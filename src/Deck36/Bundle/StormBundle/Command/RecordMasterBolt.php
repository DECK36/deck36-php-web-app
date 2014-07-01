<?php 

namespace Deck36\Bundle\StormBundle\Command;
 
use Deck36\Bundle\Plan9Bundle\Entity\Badge;
use Symfony\Component\DependencyInjection\Container;

require_once('storm.php');

class RecordMasterBolt extends BasicBolt
{

	// 1. Read RecordBreaker badge tuples and extract PbR (Points between Records)
    // 2. Save and update redis ZSET with PbR
    // 3. If updated record is in top 1 of ZSET, emit the badge


    private $container;
    private $userManager;
    
    private $recordMasterBadgeName;
    private $recordMasterBadgeText;
    private $recordMasterBadgeSize;
    private $recordMasterBadgeColor;
    private $recordMasterBadgeEffect;

    public function __construct(Container $container,
                                $recordMasterBadgeName,
                                $recordMasterBadgeText,
                                $recordMasterBadgeSize,
                                $recordMasterBadgeColor,
                                $recordMasterBadgeEffect
                                ) {
        parent::__construct();
        $this->container = $container; 
        $this->userManager = $container->get('fos_user.user_manager'); 

        $this->recordMasterBadgeName = $recordMasterBadgeName;
        $this->recordMasterBadgeText = $recordMasterBadgeText;
        $this->recordMasterBadgeSize = $recordMasterBadgeSize;
        $this->recordMasterBadgeColor = $recordMasterBadgeColor;
        $this->recordMasterBadgeEffect = $recordMasterBadgeEffect;       
    }


    public function process(Tuple $tuple)
    {

        $object = $tuple->values[0];

        if (!array_key_exists('badge', $object))
            return;

        $badge = $object['badge'];

        if ($badge != 'RecordBreaker') 
            return;

        $user = $object['user']['user_id'];
        $pointsBetweenRecords = $object['pbr'];
        
        $redis = $this->container->get("snc_redis.default");
        $redisKeyRecordMaster = 'plan9_RecordMaster';
        $previousPbR = intval($redis->zscore($redisKeyRecordMaster, $user));

        // check, if we have a new record
        if ($pointsBetweenRecords <= $previousPbR)
            return;
        
        // we have a new record!
       
        // update redis 
        $redis->zadd($redisKeyRecordMaster, $pointsBetweenRecords, $user);

        // get Top 1 user
        $top1User = $redis->zrevrange($redisKeyRecordMaster, 0, 0);

        // if the top 1 user is the current user, we have a winner!
        if ($top1User[0] != $user)
            return;

        // build a 'badge' 'object' 
        $date = new \DateTime();
                    
        // persist badge to database
        $userRef = $this->userManager->findUserBy(array('id' => $user));

        $badge = new Badge();
        $badge->setName($this->recordMasterBadgeName);
        $badge->setImage("");
        $badge->setCreatedAt($date);

        $userRef->addBadge($badge);
        $this->userManager->updateUser($userRef);

        // construct badge message     
        $recordMasterBadge = array();
        $recordMasterBadge['user'] = array();
        $recordMasterBadge['user']['user_id'] = $user;
        
        $recordMasterBadge['timestamp'] = $date->getTimestamp();
        
        $recordMasterBadge['type'] = 'badge';
        $recordMasterBadge['version'] = 1;
        
        $recordMasterBadge['badge'] = array();
        $recordMasterBadge['badge']['name'] = $this->recordMasterBadgeName;
        $recordMasterBadge['badge']['text'] = $this->recordMasterBadgeText;
        $recordMasterBadge['badge']['size'] = $this->recordMasterBadgeSize;
        $recordMasterBadge['badge']['color'] = $this->recordMasterBadgeColor;
        $recordMasterBadge['badge']['effect'] = $this->recordMasterBadgeEffect;
        
        $recordMasterBadge['points'] = array();
        $recordMasterBadge['points']['increment'] = 5 * $pointsBetweenRecords;

        $recordMasterBadge['action'] = array();
        $recordMasterBadge['action']['type'] = 'none';
        $recordMasterBadge['action']['amount'] = 0;

        // emit the badge
        $this->emit([$recordMasterBadge]); 


    } // process

   
}

