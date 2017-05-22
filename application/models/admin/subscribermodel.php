<?php

/*
 * Programmer Name:SK
 * Purpose:Locum Model
 * Date:03-02-2015
 */

class Subscribermodel extends CI_Model {

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
        $this->db->select('sub_id,sub_email,sub_status, is_deleted, sub_unsub_status,user_id', FALSE);
        if ($edit_id) {
            $this->db->where('sub_id', $edit_id);
        }
        $this->db->where(
                array(
                    'is_deleted' => 0,
                    'sub_unsub_status' => 1
        ));
        $this->db->order_by("sub_id", "DESC");
        $result = $this->db->get('subscribermst');
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
                $this->db->insert('subscribermst', $arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('sub_id', $edit_id);
                $this->db->update('subscribermst', $arrData);
                return $edit_id;
                break;
            case 'delete':
                break;
        }
    }

    public function get_subscribermst() {
        $this->db->select('sub_email as Subscriber');
        $query = $this->db->get('subscribermst');
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return FALSE;
        }
    }

    public function insert_csv($data) {
        $this->db->insert('subscribermst', $data);
    }

    public function get_subEmail() {
        $this->db->select('sub_email as Subscriber');
        $this->db->where('is_deleted = 0');
        $this->db->where('sub_status = 1');
        $resultarr = $this->db->get('subscribermst');
        if ($resultarr->num_rows()) {
            return $resultarr->result_array();
        }
        else
            return 0;
    }

    public function getEmail($sub_id) {
        $this->db->select('sub_id,	sub_email,sub_unsub_status', FALSE);
        $this->db->where("sub_id", $sub_id);
        $this->db->where(array('is_deleted' => 0));
        $result = $this->db->get('subscribermst');

        if ($result->num_rows()) {
            return $result->row();
        }
        else
            return 0;
    }

    public function getData1($sub_id, $email) {
        $aUpdate = array(
            "is_deleted" => 1
        );
        $this->db->where("sub_id", $sub_id);
        $this->db->update("subscribermst", $aUpdate);
        $sReturn = "You are successfully unsubscribed from the newsletter. You will no longer be able to receive the mails from Tabula.";
        return $sReturn;
    }

}

?>