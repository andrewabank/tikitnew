<?php

namespace Tikit\TikitBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('TikitTikitBundle:Default:index.html.twig', array('name' => $name));
    }
}
