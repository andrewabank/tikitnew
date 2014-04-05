<?php

namespace Tikit\TikitBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tikit\TikitBundle\Form\TikitType;
use Tikit\TikitBundle\Entity\Tikit;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CommentController extends Controller
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
     * @Route("/addcomment/", name="_tikit_addcomment")
     * @Template()
     */
    public function addcommentAction(Request $request)
    {
        if($request->isXmlHttpRequest() && $request->isMethod('POST')){
            $comment = $this->getRequest()->get('comment',false);
            $tikit_id = $this->getRequest()->get('tikit_id',false);
            $user_id = $this->getRequest()->get('user_id',false);

            $json = json_encode(array(
                'id' => 5
                ));
            $this->get('comment_model')->addComment($tikit_id, $comment, $user_id, $parent_id = 0);
            $responce = new Response($json);
            $responce->headers->set('Content-Type', 'application/json');
            return $responce;
        }
        //$form = $this->get('form.factory')->create(new TikitType());

        /*$request = $this->get('request');
        if ($request->isMethod('POST')) {
            $form->submit($request);
            if ($form->isValid()) {
                $form_data = $form->getData();
                $this->get('comment_model')->addComment($tikit_id, $comment, $user_id, $parent_id = 0);
                $this->get('session')->getFlashBag()->set('notice', 'Comment added!');
            }
        }*/

        //return array('form' => $form->createView());
    }

    /**
     * @Route("/tikits", name="_demo_tikits")
     * @Template()
     */
    public function removecommentAction()
    {
        $this->get('comment_model')->hideComment($comment_id);

        return $this->render('TikitTikitBundle:Tikit:tikits.html.twig', array(
            'current_page'  => $page['page'],
            'total_pages' => $page['total_pages'],
            'tikits' => $tikits
        ));
    }

    public function markcommentasspamAction()
    {
        $this->get('comment_model')->hideComment($comment_id,$user_id);

        return $this->render('TikitTikitBundle:Tikit:tikits.html.twig', array(
            'current_page'  => $page['page'],
            'total_pages' => $page['total_pages'],
            'tikits' => $tikits
        ));
    }
}
