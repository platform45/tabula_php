<?php

/*
 * Programmer Name:SK
 * Purpose:Locum Model
 * Date:03-02-2015
 */

class Newslettermodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Select data from locum table
     * Retrive single row if edit_id has been passed or retrive all records
     * @param type $edit_id
     * @return result array or zero if null. 
     */
    public function getData($edit_id = 0) {
        $this->db->select('newsletter_id,newsletter_title,newsletter_content,newsletter_submitted_date,newsletter_modified_date,newsletter_send_date,newsletter_active,newsletter_deleted', FALSE);
        if ($edit_id) {
            $this->db->where('newsletter_id', $edit_id);
        }
        $this->db->where(
                array(
                    'newsletter_deleted' => 0
        ));
          $this->db->order_by('newsletter_id', 'DESC');
        $result = $this->db->get('newsletters');
        if ($result->num_rows()) {
            if ($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }

    /**
     * This method can be used to insert or update record
     * @param type $action
     * @param type $arrData
     * @param type $edit_id
     * @return type int
     */
    public function action($action, $arrData = array(), $edit_id = 0) {
        switch ($action) {
            case 'insert':
                $this->db->insert('newsletters', $arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('newsletter_id', $edit_id);
                $this->db->update('newsletters', $arrData);
                return $edit_id;
                break;
            case 'delete':
                break;
        }
    }

    /**
     * Select data from locum table
     * Retrive single row if edit_id has been passed or retrive all records
     * @param type $edit_id
     * @return result array or zero if null. 
     */
    public function getSubscriberData() {
        $this->db->select('sub_id,sub_email,sub_status,is_deleted,sub_unsub_status,user_id', FALSE);
        $this->db->where(
                array(
                    'is_deleted' => 0,
                    'sub_status' => 1,
                    'sub_unsub_status' => 1
        ));
        $result = $this->db->get('subscribermst');

        if ($result->num_rows()) {
            return $result->result_array();
        }
        else
            return 0;
    }

}

?>