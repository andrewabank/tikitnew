<?php

namespace Tikit\TikitBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BlockedUsers
 */
class BlockedUsers
{
    /**
     * @var boolean
     */
    private $reason;

    /**
     * @var \DateTime
     */
    private $dateAdded;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Tikit\TikitBundle\Entity\FosUser
     */
    private $user;

    public function __construct()
    {
        $this->dateAdded = new \DateTime('now');
    }
    /**
     * Set reason
     *
     * @param boolean $reason
     * @return BlockedUsers
     */
    public function setReason($reason)
    {
        $this->reason = $reason;
    
        return $this;
    }

    /**
     * Get reason
     *
     * @return boolean 
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     * @return BlockedUsers
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;
    
        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return \DateTime 
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \Tikit\TikitBundle\Entity\FosUser $user
     * @return BlockedUsers
     */
    public function setUser(\Tikit\TikitBundle\Entity\FosUser $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Tikit\TikitBundle\Entity\FosUser 
     */
    public function getUser()
    {
        return $this->user;
    }
}
