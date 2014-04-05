<?php

namespace Tikit\TikitBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 */
class Comment
{
    const INIT_PARENT = 0;
    const STATUS_UNPUBLISHED = 0;
    const STATUS_PUBLISHED = 1;
    /**
     * @var integer
     */
    private $parentId = self::INIT_PARENT;

    /**
     * @var string
     */
    private $commentBody;

    /**
     * @var \DateTime
     */
    private $dateAdded;

    /**
     * @var boolean
     */
    private $status = self::STATUS_PUBLISHED;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Tikit\TikitBundle\Entity\FosUser
     */
    private $user;

    /**
     * @var \Tikit\TikitBundle\Entity\Tikit
     */
    private $tikit;


    public function __construct()
    {
        $this->dateAdded = new \DateTime('now');
    }
    /**
     * Set parentId
     *
     * @param integer $parentId
     * @return Comment
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    
        return $this;
    }

    /**
     * Get parentId
     *
     * @return integer 
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Set commentBody
     *
     * @param string $commentBody
     * @return Comment
     */
    public function setCommentBody($commentBody)
    {
        $this->commentBody = $commentBody;
    
        return $this;
    }

    /**
     * Get commentBody
     *
     * @return string 
     */
    public function getCommentBody()
    {
        return $this->commentBody;
    }

    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     * @return Comment
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
     * @return Comment
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
     * @return Comment
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
     * Set tikit
     *
     * @param \Tikit\TikitBundle\Entity\Tikit $tikit
     * @return Comment
     */
    public function setTikit(\Tikit\TikitBundle\Entity\Tikit $tikit = null)
    {
        $this->tikit = $tikit;
    
        return $this;
    }

    /**
     * Get tikit
     *
     * @return \Tikit\TikitBundle\Entity\Tikit 
     */
    public function getTikit()
    {
        return $this->tikit;
    }
}
