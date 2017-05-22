<?php

class Statemodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getData($edit_id = 0, $status = "") {
        $this->db->select("state_id, cou_id, state_name,status", FALSE);
        $this->db->from('state');
        if ($edit_id) {
            $this->db->where('state_id', $edit_id);
        }
        if ($status) {
            $this->db->where('status', $status);
        }

        //$this->db->join('city c', 'c.region_id = re.region_id', 'left');
        $this->db->group_by('state_id');
        $this->db->order_by("state_name", "ASC");
        $result = $this->db->get();


        if ($result->num_rows()) {
            if ($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }

    public function checkregion($region_name, $country_id, $edit_id = 0) {
        $this->db->select("state_id, state_name", FALSE);
        if ($edit_id) {
            $this->db->where('state_id != ', $edit_id);
        }
        $this->db->where(
                array(
                    'state_name' => $region_name,
                    'country_id' => $country_id,
        ));
        $result = $this->db->get('state');

        if ($result->num_rows()) {
            if ($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }

    public function action($action, $arrData = array(), $edit_id = 0) {
        switch ($action) {
            case 'insert':
                $this->db->insert('state', $arrData);
                $insert_id = $this->db->insert_id();
                return $insert_id;
                break;
            case 'update':
                $this->db->where('state_id', $edit_id);
                $this->db->update('state', $arrData);
                return $edit_id;
                break;
            case 'delete':
                $this->db->delete('state', array('state_id' => $edit_id));
                break;
        }
    }

}

?>