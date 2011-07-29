<?php

class Application_Model_DbTable_Contests extends Zend_Db_Table_Abstract
{

    protected $_name = 'contests';

    public function getContest($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('entry_id = ' . $id);
        if (!$row) {
            throw new Exception("Could not find row $id");
        }
        return $row;
    }
    public function addContest($form)
    {
        $insert_data = array(
                'entry_id' => $form['id'],
                'link' => $form['link'],
                'title' => $form['title'],
                'summary' => $form['summary'],
                'category' => $form['category']
                );
        $this->insert($insert_data);
    }
    public function checkContest($id)
    {
        //$id = (int)$id;
        $id = is_numeric($id) ? $id : '' ;
        $row = $this->fetchRow('entry_id = ' . $id);
        if (!$row) {
            return 'empty';
        } else {
            return 'exists';
        }
    }

}

