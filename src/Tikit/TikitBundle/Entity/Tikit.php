<?php

namespace Tikit\TikitBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tikit
 */
class Tikit
{
    const STATUS_BLOCKED = 0;
    const STATUS_DISPLAY_PUBLIC = 1;
    const STATUS_DISPLAY_FRIENDS = 2;
    const STATUS_DISPLAY_PRIVATE = 3;
    
    const COMMENTCOUNT = 0;
    
    const SCORE = 1;
    /**
     * @var string
     */
    private $tikitTitle;

    /**
     * @var string
     */
    private $tikitUrl;

    /**
     * @var integer
     */
    private $score = self::SCORE;

    /**
     * @var integer
     */
    private $commentCount = self::COMMENTCOUNT;

    /**
     * @var \DateTime
     */
    private $dateAdded;

    /**
     * @var boolean
     */
    private $status = self::STATUS_DISPLAY_PUBLIC;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Tikit\TikitBundle\Entity\FosUser
     */
    private $user;

    /**
     * @var \Tikit\TikitBundle\Entity\Category
     */
    private $category;

    public function __construct()
    {
        $this->dateAdded = new \DateTime('now');
    }
    /**
     * Set tikitTitle
     *
     * @param string $tikitTitle
     * @return Tikit
     */
    public function setTikitTitle($tikitTitle)
    {
        $this->tikitTitle = $tikitTitle;
    
        return $this;
    }

    /**
     * Get tikitTitle
     *
     * @return string 
     */
    public function getTikitTitle()
    {
        return $this->tikitTitle;
    }

    /**
     * Set tikitUrl
     *
     * @param string $tikitUrl
     * @return Tikit
     */
    public function setTikitUrl($tikitUrl)
    {
        $this->tikitUrl = $tikitUrl;
    
        return $this;
    }

    /**
     * Get tikitUrl
     *
     * @return string 
     */
    public function getTikitUrl()
    {
        return $this->tikitUrl;
    }

    /**
     * Set score
     *
     * @param integer $score
     * @return Tikit
     */
    public function setScore($score)
    {
        $this->score = $score;
    
        return $this;
    }

    /**
     * Get score
     *
     * @return integer 
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set commentCount
     *
     * @param integer $commentCount
     * @return Tikit
     */
    public function setCommentCount($commentCount)
    {
        $this->commentCount = $commentCount;
    
        return $this;
    }

    /**
     * Get commentCount
     *
     * @return integer 
     */
    public function getCommentCount()
    {
        return $this->commentCount;
    }

    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     * @return Tikit
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
     * @return Tikit
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
     * @return Tikit
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

    /**
     * Set category
     *
     * @param \Tikit\TikitBundle\Entity\Category $category
     * @return Tikit
     */
    public function setCategory(\Tikit\TikitBundle\Entity\Category $category = null)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * Get category
     *
     * @return \Tikit\TikitBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }
}
