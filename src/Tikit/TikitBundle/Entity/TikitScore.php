<?php

namespace Tikit\TikitBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TikitScore
 */
class TikitScore
{
    /**
     * @var \DateTime
     */
    private $dateAdded;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Tikit\TikitBundle\Entity\Tikit
     */
    private $tikit;

    /**
     * @var \Tikit\TikitBundle\Entity\FosUser
     */
    private $user;

    /**
     * @var integer
     */
    private $vote;


    public function __construct($tikit_id,$user_id,$vote)
    {
        $this->dateAdded = new \DateTime('now');
        $this->vote = $vote;
        $this->user = $user_id;
        $this->tikit = $tikit_id;
    }
    /**
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     * @return TikitScore
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
     * Set tikit
     *
     * @param \Tikit\TikitBundle\Entity\Tikit $tikit
     * @return TikitScore
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

    /**
     * Set user
     *
     * @param \Tikit\TikitBundle\Entity\FosUser $user
     * @return TikitScore
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
     * Set vote
     *
     * @param integer
     * @return TikitScore
     */
    public function setVote($vote)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * Get vote
     *
     * @return integer
     */
    public function getVote()
    {
        return $this->vote;
    }
}
