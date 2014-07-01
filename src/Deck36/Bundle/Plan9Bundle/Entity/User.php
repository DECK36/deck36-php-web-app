<?php

namespace Deck36\Bundle\Plan9Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as AbstractUser;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity
 */
class User extends AbstractUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var array
     *
     * @ORM\OneToMany(targetEntity="Badge", mappedBy="user", cascade={"all"},fetch="LAZY")
     */
    protected $badges;

    /**
     * @var integer
     *
     * @ORM\Column(name="points", type="integer")
     */
    protected $points = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="level", type="integer")
     */
    protected $level = 1;

    /**
     * Construct
     */
    public function __construct() {
        $this->badges = new ArrayCollection();

        parent::__construct();
    }

   

    /**
     * Add badge
     *
     * @param mixed $badge
     * @return User
     */
    public function addBadge(Badge $badge)
    {
        $badge->setUser($this);
        $this->badges->add($badge);

        return $this;
    }


    /**
     * Get badges
     *
     * @return array
     */
    public function getBadges()
    {
        return $this->badges;
    }

    /**
     * Set points
     *
     * @param integer $points
     * @return User
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer
     */
    public function getPoints()
    {
        return $this->points;
    }


    /**
     * Add points
     *
     * @param integer $points
     * @return User
     */
    public function addPoints($points)
    {
        $this->points = $this->points + $points;

        return $this;
    }



    /**
     * Set level
     *
     * @param integer $level
     * @return User
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }
}
