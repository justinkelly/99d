<?php

class Application_Model_DbTable_Applications extends Zend_Db_Table_Abstract
{

    protected $_name = 'applications';

    public function getApplication($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('entry_id = ' . $id);
        if (!$row) {
            throw new Exception("Could not find row $id");
        }
        return $row;
    }

    public function getApplications($id)
    {
        $select = $this->select()
                ->setIntegrityCheck(false)
                ->from('applications')
                ->join('contests', 'applications.contest_id = contests.entry_id')
                ->where('applications.user_id  = ?', $id);
        $options = $this->fetchAll($select);

        return $options;
    }
    public function addApplication($form)
    {
        $insert_data = array(
                'contest_id' => $form['contest_id'],
                'user_id' => $form['user_id']
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

