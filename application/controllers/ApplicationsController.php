<?php

class ApplicationsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
        $entry = new Application_Model_DbTable_Applications();
        $this->view->applications = $entry->getApplications(Zend_Registry::get('fbUser'));
    }


}

