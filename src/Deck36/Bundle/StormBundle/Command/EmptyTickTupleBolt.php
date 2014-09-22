<?php 

namespace Deck36\Bundle\StormBundle\Command;
 
use Deck36\Bundle\Plan9Bundle\Entity\Badge;
use Symfony\Component\DependencyInjection\Container;

require_once('storm.php');

class EmptyTickTupleBolt extends BasicBolt
{

	private $container;
    private $userManager;

    private $emptyTickTupleBadgeName;
    private $emptyTickTupleBadgeText;
    private $emptyTickTupleBadgeSize;
    private $emptyTickTupleBadgeColor;
    private $emptyTickTupleBadgeEffect;
    
	public function __construct(Container $container,
                                $emptyTickTupleBadgeName,
                                $emptyTickTupleBadgeText,
                                $emptyTickTupleBadgeSize,
                                $emptyTickTupleBadgeColor,
                                $emptyTickTupleBadgeEffect
                                ) {
        parent::__construct();
		$this->container = $container;
        $this->userManager = $container->get('fos_user.user_manager'); 

        $this->emptyTickTupleBadgeName = $emptyTickTupleBadgeName;
        $this->emptyTickTupleBadgeText = $emptyTickTupleBadgeText;
        $this->emptyTickTupleBadgeSize = $emptyTickTupleBadgeSize;
        $this->emptyTickTupleBadgeColor = $emptyTickTupleBadgeColor;
        $this->emptyTickTupleBadgeEffect = $emptyTickTupleBadgeEffect;        
	}


    private function isTickTuple(Tuple $tuple) {
        return (($tuple->component === '__system') && ($tuple->stream === '__tick'));
    }


    private function processTickTuple(Tuple $tuple) {
        // process tick tuple
        // for demonstration purposes we send
        // an empty badge message  

        $date = new \DateTime();

        // as we do not get any regular tuples, 
        // we do not know any userid's
        $targetUser = 0;

        // persist badge to database
        // we skip this step for the empty tick tuple
        // in order to not bloat the database with 
        // irrelevant badge entries
        /*
        $userRef = $this->userManager->findUserBy(array('id' => $targetUser));

        $badge = new Badge();
        $badge->setName($this->emptyTickTupleBadgeName);
        $badge->setImage("");
        $badge->setCreatedAt($date);

        $userRef->addBadge($badge);
        $this->userManager->updateUser($userRef);
        */


        // construct badge message            
        $emptyTickTupleBadge = array();        
        $emptyTickTupleBadge['user'] = array();
        $emptyTickTupleBadge['user']['user_id'] = $targetUser;
        
        $emptyTickTupleBadge['timestamp'] = $date->getTimestamp();

        $emptyTickTupleBadge['type'] = 'badge';
        $emptyTickTupleBadge['version'] = 1;
        
        $emptyTickTupleBadge['badge'] = array();
        $emptyTickTupleBadge['badge']['name'] = $this->emptyTickTupleBadgeName;
        $emptyTickTupleBadge['badge']['text'] = $this->emptyTickTupleBadgeText;
        $emptyTickTupleBadge['badge']['size'] = $this->emptyTickTupleBadgeSize;
        $emptyTickTupleBadge['badge']['color'] = $this->emptyTickTupleBadgeColor;
        $emptyTickTupleBadge['badge']['effect'] = $this->emptyTickTupleBadgeEffect;
        
        $emptyTickTupleBadge['points'] = array();
        $emptyTickTupleBadge['points']['increment'] = 0;            

        $emptyTickTupleBadge['action'] = array();
        $emptyTickTupleBadge['action']['type'] = 'none';
        $emptyTickTupleBadge['action']['amount'] = 0;

        // emit the badge.
        // we emit unanchored, 
        // because we don't anchor to ephemeral tick tuples
        $this->emit([$emptyTickTupleBadge]);   

    }

    private function processTuple(Tuple $tuple) {
        // process regular tuples
    }


    public function process(Tuple $tuple)
    {
        // check for tick tuple
        if ($this->isTickTuple($tuple)) {            
            $this->processTickTuple($tuple);
        } else {
            $this->processTuple($tuple);
        }      

        $this->ack($tuple);
    }
   
}

