<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->fbConnect();

    }
    public function fbConnect()
    {

        switch(getenv('APPLICATION_ENV'))
        {
        case 'development':
            Zend_Registry::set('fbAppId', '128075177284337');
            Zend_Registry::set('fbApikey','128075177284337');
            Zend_Registry::set('fbSecret','66e2fe797c7cbc934ca0e3a26a1eec82');
            Zend_Registry::set('fb_baseurl', 'http://localhost.local/99d/public');
        break;
        case 'test':
            Zend_Registry::set('fbAppId', '230808733626136');
            Zend_Registry::set('fbApikey','230808733626136');
            Zend_Registry::set('fbSecret','ecea142cda55c3c7815752e9eca3123c');
            Zend_Registry::set('fb_baseurl', 'http://kelly.org.au/dev/99d/public');
        }
        $facebook = new Facebook(array(
            'appId'  => Zend_Registry::get('fbAppId'),
            'secret' => Zend_Registry::get('fbSecret'),
            'cookie' => true
        ));

        Zend_Registry::set('fbUser', $facebook->getUser());
        Zend_Registry::set('facebook', $facebook);

        if (Zend_Registry::get('fbUser')) {
            try {
                // Proceed knowing you have a logged in user who's authenticated.
                Zend_Registry::set('fbuser_profile', $facebook->api('/me'));
                $view->fbuser_profile = Zend_Registry::get('fbuser_profile');
            } catch (FacebookApiException $e) {
                error_log($e);
                Zend_Registry::set('fbuser_profile', null);
                Zend_Registry::set('fbUser', null);
            }
        }

        if (Zend_Registry::get('fbUser')) {
            Zend_Registry::set('fbUrl', $facebook->getLogoutUrl(array('next'=> Zend_Registry::get('fb_baseurl') . "/fb/logout")));
        } else {
            Zend_Registry::set('fbUrl', $facebook->getLoginUrl(
                array(
                    'display'   => 'popup',
                    'scope'         => 'email,offline_access,user_birthday,user_location,user_about_me,user_hometown,publish_stream',
                    'next'      =>  Zend_Registry::get('fb_baseurl') . "/fb/login",
                    'cancel_url'      =>  Zend_Registry::get('fb_baseurl') . "/test/fsetse",
                    'redirect_uri' => Zend_Registry::get('fb_baseurl') . "/fb/login"
                )
            ));
        }

        $view->fbUrl = Zend_Registry::get('fbUrl');
        $view->fbuser = Zend_Registry::get('fbUser');
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
            $redirect_url =  $this->view->url(array('controller'=>'index','action'=>'applications'),null,true);              
            $this->view->headMeta()->appendHttpEquiv('Refresh', "3;URL=$redirect_url");
        }

        $this->view->deal=$deal;
        $this->view->message = $message; 
        $this->view->contest = $entry_details; 

    }


}





