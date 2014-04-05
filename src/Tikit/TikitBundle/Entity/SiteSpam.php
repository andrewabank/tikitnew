<?php

namespace Tikit\TikitBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SiteSpam
 */
class SiteSpam
{
    const NOT_PROCESSED = 0;
    const PROCESSED = 1;
    
    const DEFAULT_ZERO = 0;
    /**
     * @var integer
     */
    private $tikitId = self::DEFAULT_ZERO;

    /**
     * @var integer
     */
    private $commentId = self::DEFAULT_ZERO;

    /**
     * @var \DateTime
     */
    private $dateAdded;

    /**
     * @var boolean
     */
    private $status = self::NOT_PROCESSED;

    /**
     * @var \DateTime
     */
    private $dateChanged;

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
        $this->dateChanged = new \DateTime('now');
        $this->dateAdded = new \DateTime('now');
    }
    /**
     * Set tikitId
     *
     * @param integer $tikitId
     * @return SiteSpam
     */
    public function setTikitId($tikitId)
    {
        $this->tikitId = $tikitId;
    
        return $this;
    }

    /**
     * Get tikitId
     *
     * @return integer 
     */
    public function getTikitId()
    {
        return $this->tikitId;
    }

    /**
     * Set commentId
     *
     * @param integer $commentId
     * @return SiteSpam
     */
    public function setCommentId($commentId)
    {
        $this->commentId = $commentId;
    
        return $this;
    }

    /**
     * Get commentId
     *
     * @return integer 
     */
    public function getCommentId()
    {
        return $this->commentId;
    }

    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     * @return SiteSpam
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
     * Set status
     *
     * @param boolean $status
     * @return SiteSpam
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set dateChanged
     *
     * @param \DateTime $dateChanged
     * @return SiteSpam
     */
    public function setDateChanged($dateChanged)
    {
        $this->dateChanged = $dateChanged;
    
        return $this;
    }

    /**
     * Get dateChanged
     *
     * @return \DateTime 
     */
    public function getDateChanged()
    {
        return $this->dateChanged;
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
     * @return SiteSpam
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
