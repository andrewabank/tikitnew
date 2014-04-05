<?php
// Service Code
namespace Tikit\TikitBundle\Service;

use Doctrine\ORM\Mapping as ORM;
use Tikit\TikitBundle\Entity\Following;

class FollowModel
{
    protected $em;
 
    public function __construct($em)
    {
        $this->em = $em;
    }    
 
    /**
     * Gets Symfony-Barcelona info from Sensio Connect. Info is stored in APC during an hour to increase speed
     * @return array
     */    
    public function addFollowing($user_id, $follower_id)
    {
        $following = new Following;
        $user = $this->em->find('\Tikit\TikitBundle\Entity\FosUser', $user_id);
        $following->setUser($user);
        $user = $this->em->find('\Tikit\TikitBundle\Entity\FosUser', $follower_id);
        $following->setFollower($user);
        $this->em->persist($following);
        $this->em->flush();
    }
    
    public function unFollow($user_id, $follower_id)
    {
        $following = $this->em->getRepository('Tikit\TikitBundle\Entity\Following')->findOneBy(array('user' => $user_id, 'follower' => $follower_id));

        $this->em->remove($following);
        $this->em->flush();
    }
}