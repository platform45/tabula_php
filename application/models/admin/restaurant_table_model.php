<?php

/*
  Model that contains function related to restaurant
 */

class Restaurant_table_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /*
     * Method Name: get_tables
     * Purpose: To get tables for a restaurant from database
     * params:
     *      input: restaurant_id
     *      output: array of restaurant tables
     */

    public function get_tables($restaurant_id) {
        $this->db->select("*");
        $this->db->from("restaurant_tables");
        $data = array(
            'user_id' => $restaurant_id,
            'is_deleted' => '0'
        );
        $this->db->where($data);
        $query = $this->db->get();

        return ( $query->num_rows() > 0 ) ? $query->result() : '';
    }

    /*
     * Method Name: get_tables
     * Purpose: To get tables for a restaurant from database
     * params:
     *      input: restaurant_id
     *      output: array of restaurant tables
     */

    public function action($action, $arrData = array(), $edit_id = 0) {
        switch ($action) {
            case 'insert':
                $this->db->insert('restaurant_tables', $arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('table_id', $edit_id);
                $this->db->update('restaurant_tables', $arrData);
                return $edit_id;
                break;
        }
    }

    /*
     * Method Name: get_table_data
     * Purpose: To data of particular table
     * params:
     *      input: table_id
     *      output: array of table data
     */

    public function get_table_data($table_id) {
        $this->db->select("*");
        $this->db->from("restaurant_tables");
        $data = array(
            'table_id' => $table_id,
            'is_deleted' => '0',
            'status' => '1'
        );
        $this->db->where($data);
        $this->db->limit(1);
        $query = $this->db->get();

        return ( $query->num_rows() > 0 ) ? $query->row() : '';
    }

    /*
     * Method Name: check_table_exists
     * Purpose: To check is table already exists
     * params:
     *      input: user_id, table_name, table_id
     *      output: TRUE/FALSE
     */

    public function check_table_exists($user_id, $table_name, $table_id = 0) {
        $this->db->select("table_id");
        $this->db->from("restaurant_tables");
        $data = array(
            'user_id' => $user_id,
            'table_name' => $table_name,
            'is_deleted' => '0',
            'status' => '1'
        );
        $this->db->where($data);
        if ($table_id > 0)
            $this->db->where("table_id != $table_id");

        $this->db->limit(1);
        $query = $this->db->get();
        return ( $query->num_rows() > 0 ) ? TRUE : FALSE;
    }

}

?>