<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */

    }

    public function indexAction()
    {
        // action body
        //
        $channel = new Zend_Feed_Atom('http://99designs.com/feed');
        $this->view->channel = $channel;

        //get menu
        $menu = array();

        foreach ($channel as $item) {

            $id = substr($item->id(),-5);
            //check if item in db already
            $entry = new Application_Model_DbTable_Contests();
            if($entry->checkContest($id)!='exists')
            {
                //add items to db
                $form = array(
                    'id'       => $id,
                    'link'     => $item->link['href'],
                    'title'    => $item->title,
                    'summary'  => $item->summary,
                    'category' => $item->category['term']
                );
                $entry->addContest($form);
            }
            //build menu
            if(!in_array($item->category['term'],$menu))
            {
                $menu[]=$item->category['term'];
            }
        }
        $this->view->menu = $menu;
    }

    public function contestAction()
    {
        // action body
        //get id from url
        $id = $this->_getParam('id');
        $entry = new Application_Model_DbTable_Contests();
        $this->view->contest = $entry->getContest($id);
    }

    public function applyAction()
    {
        $progress='go';

        $id = $this->_getParam('id');
        $entry = new Application_Model_DbTable_Contests();
        $entry_details = $entry->getContest($id);
        $this->view->contest = $entry_details;
        // action body
        if (!Zend_Registry::get('fbUser')) {
            $message = "You need to be logged in via Facebook to pocket this deal. <br /><br />Please login using the Facebook login link at the top of the page";
            $progress = 'stop';
        } 

        if( $progress=='go')
        {
            $facebook = Zend_Registry::get('facebook');

            try {
                $publishStream = $facebook->api("/".Zend_Registry::get('fbUser')."/feed", 'post', array(
                    'message' => "Just applied for the 99Designs contest  $entry_details->title",
                    'link'    => $entry_details->link,
                    'picture' => 'http://css2.99static.com/static/images/99designs-logo-r-180px.png',
                    'name'    => '99Designs',
                    'description'=> $entry_details->summary
                )
            );
                $message = "You just applied for the the 99Designs contest  $entry_details->title";
                //as $_GET['publish'] is set so remove it by redirecting user to the base url
            } catch (FacebookApiException $e) {
                var_dump($e);
            }
            //$this->_view->redirect('my-pocket');a
            //
            //applications

            $app_array = array(
                'contest_id'=>$id,
                'user_id'=>Zend_Registry::get('fbUser')
                );
            $apply = new Application_Model_DbTable_Applications();
            $apply->addApplication($app_array);

            $redirect_url =  $this->view->url(array('controller'=>'applications','action'=>'index'),null,true);              
            $this->view->headMeta()->appendHttpEquiv('Refresh', "3;URL=$redirect_url");
        }

        $this->view->deal=$deal;
        $this->view->message = $message; 
        $this->view->contest = $entry_details; 

    }


}





