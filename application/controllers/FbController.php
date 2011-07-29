<?php

class FbController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->_helper->layout->setLayout('fb');
    }

    public function indexAction()
    {
        // action body
    }

    public function logoutAction()
    {
        // action body
        //
        $_SESSION['fb_' .Zend_Registry::get('fbAppId'). '_code'] =''; 
        unset($_SESSION['fb_' .Zend_Registry::get('fbAppId'). '_access_token']); 
        unset($_SESSION['fb_' .Zend_Registry::get('fbAppId'). '_user_id']); 
        $this->_helper->redirector('index','index');

    }
    public function loginAction()
    {    
        //get the user

        $error = $this->_getParam('error_reason');
        if($error !== '')
        {
            $this->view->facebook_error=$error;
        } else {
            $this->view->facebook_error='';
        }

        //now decode them, make them array and sort based on keys
        $sortArray = get_object_vars(json_decode($_GET['session']));
        ksort($sortArray);

        $strCookie  =   "";
        $flag       =   false;
        foreach($sortArray as $key=>$item){
            if ($flag) $strCookie .= '&';
            $strCookie .= $key . '=' . $item;
            $flag = true;
        }

        //now set the cookie so that next time user don't need to click login again
        setCookie('fbs_' . "{$fbconfig['appid']}", $strCookie);

        //if in db redure else add
        //add to db
        //redirector to start
        $this->view->logged_in_with_facebook='true';
//       $this->_helper->redirector('index','pages');

    }

}





