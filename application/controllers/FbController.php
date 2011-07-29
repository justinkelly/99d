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
        $_SESSION['fb_132787250133331_code'] =''; 
        unset($_SESSION['fb_132787250133331_access_token']); 
        unset($_SESSION['fb_132787250133331_user_id']); 
     $this->_helper->redirector('index','pages');

    }
    public function loginAction()
    {    
        //get the user
        //see if already in the database
        if (Zend_Registry::get('fbUser')) {
            $user = new Application_Model_DbTable_Users();
            $fbuser_profile = Zend_Registry::get('fbuser_profile');
            if($user->checkUser($fbuser_profile['id']))
            {
                //
            } else {
                // add refer ID
                //if come from referal url - add into share log
                $fbuser_profile['first_login'] ='true';
                $user->addFBUser($fbuser_profile);
                //redirect to hwo to page
                $this->view->first_facebook_login='true';
            }
        }

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





