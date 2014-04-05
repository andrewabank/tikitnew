<?php

namespace Tikit\TikitBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SpamUserCount
 */
class SpamUserCount
{
    /**
     * @var integer
     */
    private $spamNumber = 1;

    /**
     * @var \DateTime
     */
    private $dateUpdated;

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
        $this->dateUpdated = new \DateTime('now');
    }
    /**
     * Set spamNumber
     *
     * @param integer $spamNumber
     * @return SpamUserCount
     */
    public function setSpamNumber($spamNumber)
    {
        $this->spamNumber = $spamNumber;
    
        return $this;
    }

    /**
     * Get spamNumber
     *
     * @return integer 
     */
    public function getSpamNumber()
    {
        return $this->spamNumber;
    }

    /**
     * Set dateUpdated
     *
     * @param \DateTime $dateUpdated
     * @return SpamUserCount
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
    
        return $this;
    }

    /**
     * Get dateUpdated
     *
     * @return \DateTime 
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
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
     * @return SpamUserCount
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
