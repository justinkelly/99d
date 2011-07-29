<?php

class Application_Model_DbTable_Users extends Zend_Db_Table_Abstract
{

    protected $_name = 'users';

    public function getUser($fbid)
    {
        $id = is_numeric($fbid) ? $fbid : '' ;
        $row = $this->fetchRow('facebook_id = ' . $id);
        if (!$row) {
            throw new Exception("Could not find row $id");
        }
        return $row;
    }
    public function addUser($form)
    {

                $form_data['facebook_id'] = $form['facebook_id'];

        $this->insert($form_data);
    }

}

