<?php
// Service Code
namespace Acme\DemoBundle\Service;

use Doctrine\ORM\Mapping as ORM;
use Acme\DemoBundle\Entity\Tikit;

class TikitModel
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
    
    public function getTotalTikits()
    {
        $count = $this->em->createQuery('SELECT COUNT(DISTINCT t.id) FROM \Acme\DemoBundle\Entity\Tikit t WHERE t.status = 1 AND t.display = 1')
                    ->getSingleResult();
        return $count[1];
    }
    
    
    public function getTikits($count_per_page,$offset)
    {
        $query = $this->em->createQuery('SELECT t, u.username FROM \Acme\DemoBundle\Entity\Tikit t
                                    LEFT JOIN \Acme\DemoBundle\Entity\FosUser u WITH u.id = t.user
                                    AND t.status = 1 AND t.display = 1')
                    ->setMaxResults($count_per_page)
                    ->setFirstResult($offset);
        $tikits = $query->getResult();
        return $tikits;
    }    
}