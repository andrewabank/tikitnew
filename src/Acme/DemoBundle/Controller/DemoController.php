<?php

namespace Acme\DemoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Acme\DemoBundle\Form\ContactType;
use Acme\DemoBundle\Form\TikitType;
use Acme\DemoBundle\Entity\Tikit;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DemoController extends Controller
{
    /**
     * @Route("/", name="_demo")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/hello/{name}", name="_demo_hello")
     * @Template()
     */
    public function helloAction($name)
    {
        return array('name' => $name);
    }

    /**
     * @Route("/contact", name="_demo_contact")
     * @Template()
     */
    public function contactAction()
    {
        $form = $this->get('form.factory')->create(new ContactType());

        $request = $this->get('request');
        if ($request->isMethod('POST')) {
            $form->submit($request);
            if ($form->isValid()) {
                $mailer = $this->get('mailer');
                // .. setup a message and send it
                // http://symfony.com/doc/current/cookbook/email.html

                $this->get('session')->getFlashBag()->set('notice', 'Message sent!');

                return new RedirectResponse($this->generateUrl('_demo'));
            }
        }

        return array('form' => $form->createView());
    }
     /**
     * @Route("/addtikit", name="_demo_addtikit")
     * @Template()
     */
    public function addtikitAction()
    {
        $form = $this->get('form.factory')->create(new TikitType());

        $request = $this->get('request');
        if ($request->isMethod('POST')) {
            $form->submit($request);
            if ($form->isValid()) {
                
                //$mailer = $this->get('mailer');
                // .. setup a message and send it
                // http://symfony.com/doc/current/cookbook/email.html
                $form_data = $form->getData();
                $tikit = new Tikit();
                $tikit->setTikitTitle($form_data['tikit_name']);
                $tikit->setTikitUrl($form_data['tikit_url']);
                $tikit->setDateAdded(new \DateTime('2011-02-19 19:22:44'));
               // $tikit->setDateAdded('2011-02-19 19:22:44');
                
                //$tikit->setUser(1);
                $em = $this->getDoctrine()->getManager();
                $category = $em->find('\Acme\DemoBundle\Entity\Category', 1);
                $tikit->setCategory($category);
                $user = $em->find('\Acme\DemoBundle\Entity\FosUser', 1);
                $tikit->setUser($user);
                $em->persist($tikit);
                $em->flush();
                $this->get('session')->getFlashBag()->set('notice', 'Tikit added!');
                
                return array('form' => $form->createView());
                //return new RedirectResponse($this->generateUrl('_demo'));
            }
        }

        return array('form' => $form->createView());
    }
    
    /**
     * @Route("/tikits", name="_demo_tikits")
     * @Template()
     */
    public function tikitsAction()
    {
        $request = $this->get('request');
        $page = $request->get('page');
        $total_count = $this->get('tikit_model')->getTotalTikits();
        $page = $this->get('util_model')->getPageData($total_count,$page);
        $tikits = $this->get('tikit_model')->getTikits($page['count_per_page'],$page['offset']);
        return $this->render('AcmeDemoBundle:Demo:tikits.html.twig', array(
            'current_page'  => $page['page'],
            'total_pages' => $page['total_pages'],
            'tikits' => $tikits
        ));
    }
}
