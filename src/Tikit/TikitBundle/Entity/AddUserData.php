<?php

namespace Tikit\TikitBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AddUserData
 */
class AddUserData
{
    /**
     * @var integer
     */
    private $score;

    /**
     * @var \DateTime
     */
    private $dateSatusUpdated;

    /**
     * 
     * @var integer
    *
     */
    private $id;

    /**
     * @var \Tikit\TikitBundle\Entity\FosUser
     */
    private $user;

    public function __construct()
    {
        $this->dateSatusUpdated = new \DateTime('now');
    }
    /**
     * Set score
     *
     * @param integer $score
     * @return AddUserData
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
     * Set dateSatusUpdated
     *
     * @param \DateTime $dateSatusUpdated
     * @return AddUserData
     */
    public function setDateSatusUpdated($dateSatusUpdated)
    {
        $this->dateSatusUpdated = $dateSatusUpdated;
    
        return $this;
    }

    /**
     * Get dateSatusUpdated
     *
     * @return \DateTime 
     */
    public function getDateSatusUpdated()
    {
        return $this->dateSatusUpdated;
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
     * @return AddUserData
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
