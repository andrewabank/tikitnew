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

class TikitController extends Controller
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
     * @Route("/addtikit/tikit", name="_tikit_addtikit")
     * @Template()
     */
    public function addtikitAction()
    {
        $form = $this->get('form.factory')->create(new TikitType());

        $request = $this->get('request');
        if ($request->isMethod('POST')) {
            $form->submit($request);
            if ($form->isValid()) {
                $form_data = $form->getData();
                $this->get('tikit_model')->addTikit($form_data);
                $this->get('session')->getFlashBag()->set('notice', 'Tikit added!');
                return array('form' => $form->createView());
            }
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/tikits/{page}", name="tikit_tikits")
     * @Template()
     */
    public function tikitsAction($page)
    {
        $request = $this->get('request');
        $page = $request->get('page');
        $total_count = $this->get('tikit_model')->getTotalTikits();
        $page = $this->get('util_model')->getPageData($total_count,$page);
        //$tikits = $this->get('tikit_model')->getAllTikits($page['count_per_page'],$page['offset']);
        $tikits = $this->get('tikit_model')->getTikitsByCategory($page['count_per_page'],$page['offset'],1);
        //$this->get('tikit_model')->markTikitAsSpam(3,1);
        //$this->get('tikit_model')->processTikitAsNotSpam(3);
        //$this->get('tikit_model')->processTikitAsSpam(2,1);
        //$tiki = $this->get('comment_model')->addComment(1, "first com'ment", 1, 0);
        //$pagefollow = $this->get('follow_model')->unFollow(1,4);
        //$pagefollow = $this->get('follow_model')->addFollowing(1,4);
        //$this->get('comment_model')->hideComment(1);
        //$this->get('tikit_model')->addTikitScore(5,1);
        //$this->get('tikit_model')->processTikitAsNotSpam(2,1);
        //$this->get('tikit_model')->removeTikitScore(5,1);
        return $this->render('TikitTikitBundle:Tikit:tikits.html.twig', array(
            'current_page'  => $page['page'],
            'total_pages' => $page['total_pages'],
            'tikits' => $tikits
        ));
    }

    /**
     * @Route("/t/{id}", name="tikit_tikit")
     * @Template()
     */
    public function tikitAction($id)
    {
        $request = $this->get('request');
        $id = $request->get('id');
        //$tikit = $this->get('tikit_model')->getTikit($id);
        //$tikits = $this->get('tikit_model')->getTikitsByCategory($page['count_per_page'],$page['offset'],1);
        //var_dump($tikit);
        $tikit = $this->get('tikit_model')->getTikit($id);
        $comments = $this->get('comment_model')->getTikitComments($id);
        return $this->render('TikitTikitBundle:Tikit:tikit.html.twig', array(
            //'current_page'  => $page['page'],
            //'total_pages' => $page['total_pages'],
            'tikit' => $tikit[0][0],
            'user' => $tikit[0],
            'comments' => $comments
        ));
    }

     /**
     * @Route("/addtikitvote/", name="_tikit_addtikitvote")
     * @Template()
     */
    public function voteAction(Request $request)
    {
        if($request->isXmlHttpRequest() && $request->isMethod('POST')){
            $vote = $this->getRequest()->get('vote',false);
            $tikit_id = $this->getRequest()->get('tikit_id',false);
            $user_id = $this->getRequest()->get('user_id',false);

            $res = $this->get('tikit_model')->voteTikit($tikit_id,$user_id,$vote);
            if($res){
                $json = array('id' => 1);
            } else{
                $json = array('id' => 0);
            }
            $json = json_encode($json);
            $response = new Response($json);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
    }

     /**
     * @Route("/loadlinkdata/", name="_tikit_loadlinkdata")
     * @Template()
     */
	public function loadlinkdataAction(Request $request){
        if($request->isXmlHttpRequest() && $request->isMethod('POST')){
            $url = $this->getRequest()->get('url',false);
            if (!empty($url)) {
                try{
                    $options = array(
                        'timeout' => 30,
                        //'max_imagesize' => 512 * 1024,
                        'max_imagesize' => 0,
                        //'min_imagesize' => 1 * 1024,
                        'min_imagesize' => 0,
                        'max_title_length' => 200,
                        'max_description_length' => 500,
                        'min_width' => 50,
                        'min_height' => 50,
                    );
                    $data = $this->get('tikit_model')->linkRequestContents($url, $options);
                    //$data = WallService::linkRequestContents($url, $options);

                    // For video subtype, eg. video.other, video.episode, video.movie, video.other, video.tv_show
                    $types = explode('.', $data['type']);
                    $data['type'] = $types[0];
                    //$data = 1;
                    $json = array("status" => 1, "message" => 'LINK DATA SUCCESS', "data" => $data);
                }
                catch(\Exception $e)
                {
                    //logException($ex);
                    $json = array("status"=>0, "message"=>'UNEXPECTED ERROR' );//. $ex->getMessage());
                }
            }else{
                $json = array("status"=>0, "message"=>'INVALID. EMPTY URL');
            }
		}else{
			$json = array("status"=>0, "message"=>"zzzzzzzz... don't play with our server!");
		}
        $json = json_encode($json);
        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
	}


}
