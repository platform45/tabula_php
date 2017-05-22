<?php

/*
 * Programmer Name:Akash Deshmukh
 * Purpose: Countries Model
 * Date: 02 Sept 2016
 * Dependency: countriesmodel.php
 */

class Countriesmodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    //Method to get the country lists
    public function getData($edit_id = 0, $status = "") {
        $this->db->select("c.*, COUNT(re.region_id) as region_count", FALSE);
        $this->db->from('country c');
        if ($edit_id) {
            $this->db->where('c.cou_id', $edit_id);
        }
        if ($status) {
            $this->db->where('c.status', $status);
        }
        $this->db->join('region re', 're.cou_id = c.cou_id', 'left');
        $this->db->group_by('c.cou_id');
        $this->db->order_by("cou_name", "ASC");
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

    //Function to check country existence
    public function checkCountry($country_name, $edit_id = 0) {
        $this->db->select("cou_id, cou_name", FALSE);
        if ($edit_id) {
            $this->db->where('cou_id != ', $edit_id);
        }
        $this->db->where(
                array(
                    'cou_name' => $country_name
        ));
        $result = $this->db->get('country');

        if ($result->num_rows()) {
            if ($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }

    //Method to check country abbreviation exixts
    public function checkCountryAbbr($country_abbrivation, $edit_id = 0) {
        $this->db->select("cou_id, cou_abbreviation", FALSE);
        if ($edit_id) {
            $this->db->where('cou_id != ', $edit_id);
        }
        $this->db->where(
                array(
                    'cou_abbreviation' => $country_abbrivation
        ));
        $result = $this->db->get('country');

        if ($result->num_rows()) {
            if ($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }

    //Method to add,edit and delete
    public function action($action, $arrData = array(), $edit_id = 0) {
        switch ($action) {
            case 'insert':
                $this->db->insert('country', $arrData);
                $insert_id = $this->db->insert_id();
                return $insert_id;
                break;
            case 'update':
                $this->db->where('cou_id', $edit_id);
                $this->db->update('country', $arrData);
                return $edit_id;
                break;
            case 'delete':
                $this->db->delete('country', array('cou_id' => $edit_id));
                break;
        }
    }

    /*
       * Method Name: get_country_name
       * Purpose: Get country name from database
       * params:
       *      input: country id
       *      output: country name
       */
      public function get_country_name( $country )
      {
        $this->db->select("cou_name");
        $this->db->from("country");
        $data = array(
                  'cou_id' => $country
                );
        $this->db->where( $data );
        $this->db->limit(1);
        $query = $this->db->get();

        return ( $query->num_rows() > 0 ) ? $query->row()->cou_name : "";
      }

}

?>