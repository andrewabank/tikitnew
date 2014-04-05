<?php
// Service Code
namespace Acme\DemoBundle\Service;

class UtilModel
{
    protected $em;
 
    public function getPageData($total_count,$page=1,$count_per_page=10)
    {
        $total_pages = ceil($total_count/$count_per_page);
        if(!is_numeric($page)){
            $page = 1;
        } else {
            $page = floor($page);
        }
        if($total_count<=$count_per_page){
            $page = 1;
        }
        if(($page*$count_per_page)>$total_count){
            $page = $total_pages;
        }
        $offset = 0;
        if($page>1){
            $offset = $count_per_page * ($page - 1);
        }
        return array('count_per_page' => $count_per_page,'offset' => $offset, 
            'total_pages' => $total_pages, 'page' => $page);
    }    

}