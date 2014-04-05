<?php

namespace Tikit\TikitBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Following
 */
class Following
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Tikit\TikitBundle\Entity\FosUser
     */
    private $user;

    /**
     * @var \Tikit\TikitBundle\Entity\FosUser
     */
    private $follower;


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
     * @return Following
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
     * Set follower
     *
     * @param \Tikit\TikitBundle\Entity\FosUser $follower
     * @return Following
     */
    public function setFollower(\Tikit\TikitBundle\Entity\FosUser $follower = null)
    {
        $this->follower = $follower;
    
        return $this;
    }

    /**
     * Get follower
     *
     * @return \Tikit\TikitBundle\Entity\FosUser 
     */
    public function getFollower()
    {
        return $this->follower;
    }
}
