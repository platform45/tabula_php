<?php

/*
 * Programmer Name:Akash Deshmukh
 * Purpose:To add regions specific to the restaurant.
 * Date: 02 Sept 2016
 * Dependency: regions.php
 */

class Regionsmodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    //To load method to get data
    public function getData($edit_id = 0, $status = "") {
        $this->db->select("re.*, COUNT(c.city_id) as city_count,co.cou_name", FALSE);
        $this->db->from('region re');
        if ($edit_id) {
            $this->db->where('re.region_id', $edit_id);
        }
        if ($status) {
            $this->db->where('re.status', $status);
        }
        $this->db->join('city c', 'c.region_id = re.region_id', 'left');
        $this->db->join('country co', 'co.cou_id=re.cou_id', 'inner');
        $this->db->group_by('re.region_id');
        $this->db->order_by("region_name", "ASC");
        $result = $this->db->get();
//        echo $this->db->last_query();die;
        if ($result->num_rows()) {
            if ($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }

    //Method to check existence of region
    public function checkregion($region_name, $country_id, $edit_id = 0) {
        $this->db->select("region_id, region_name", FALSE);
        if ($edit_id) {
            $this->db->where('region_id != ', $edit_id);
        }
        $this->db->where(
                array(
                    'region_name' => $region_name,
                    'cou_id' => $country_id,
        ));
        $result = $this->db->get('region');
        if ($result->num_rows()) {
            if ($edit_id)
                return $result->row();
            else
                return $result->result_array();
        }
        else
            return 0;
    }

    //Method to add,edit and delete the regions
    public function action($action, $arrData = array(), $edit_id = 0) {
        switch ($action) {
            case 'insert':
                $this->db->insert('region', $arrData);
                $insert_id = $this->db->insert_id();
                return $insert_id;
                break;
            case 'update':
                $this->db->where('region_id', $edit_id);
                $this->db->update('region', $arrData);
                return $edit_id;
                break;
            case 'delete':
                $this->db->delete('region', array('region_id' => $edit_id));
                break;
        }
    }

    /*
       * Method Name: get_state_name
       * Purpose: Get state name from database
       * params:
       *      input: state id
       *      output: state name
       */
      public function get_state_name( $state )
      {
        $this->db->select("region_name");
        $this->db->from("region");
        $data = array(
                  'region_id' => $state
                );
        $this->db->where( $data );
        $this->db->limit(1);
        $query = $this->db->get();

        return ( $query->num_rows() > 0 ) ? $query->row()->region_name : "";
      }

    /****************************************
     * THIS FUNCTION USE TO GET CITIES COUNT
     * PARAMETER:
     * $aSearchArray: Search parameters available in this array like
     * City name.
     **********************/
    public function RegionCount($aSearchArray)
    {
        //---- SEARCH PARAMETERS ----//
        $iSearchByCountryName   = $aSearchArray[1]; /*Country Name*/
        $iSearchByRegionName   = $aSearchArray[0]; /*Region Name*/

        $this->db->select("COUNT(region_id) as NumberOfRegions",FALSE);
        $this->db->from('region');

        //---- WHERE CONDITION ----//
        if(!empty($iSearchByCountryName)) $this->db->where("cou_id", $iSearchByCountryName);
        if(!empty($iSearchByRegionName)) $this->db->where("region_name LIKE '".$iSearchByRegionName."%'");

        $result = $this->db->get();

        if($result->num_rows()){
                return $result->result_array();
        }else return 0;
    }

    /****************************************
     * GET ALL REGION
     * PARAMETER:
     * $iPageSize: No of records to be display
     * $iRecordStartFrom: Records start from 0,25,50...etc.
     * $aSearchArray: Search parameters avaialble in this array like
     * City name.
     * $aOrderByCondition : Order by array contains order by ASC/DESC and
     * Column name on which order operation perform.
     **********************/
    public function RegionListing($iPageSize,$iRecordStartFrom,$aSearchArray,$aOrderByCondition)
    {
            //---- ORDER BY ----//
            $sOrderByColumn         = $aOrderByCondition['colomn_name'];
            $sOrderBy               = $aOrderByCondition['order_by'];


            //---- SEARCH PARAMETERS ----//
            $iSearchByRegionName   = $aSearchArray[0]; /*City Name*/
            $iSearchByCountryName   = $aSearchArray[1]; /*Country Name*/


            $this->db->select("re.*,co.cou_name",FALSE);
            $this->db->from('region re');
            $this->db->join('country co', 'co.cou_id=re.cou_id', 'left');
            //$this->db->join('city c', 'c.region_id = re.region_id', 'left');
            //$this->db->group_by('re.region_id');
//$this->db->order_by("region_name","ASC");

            //---- WHERE CONDITION ----//
            if(!empty($iSearchByCountryName)) $this->db->where("re.cou_id", $iSearchByCountryName);
            if(!empty($iSearchByRegionName)) $this->db->where("re.region_name LIKE '".$iSearchByRegionName."%'");


            //---- ORDER BY CONDITION ----//
            $this->db->order_by($sOrderByColumn,$sOrderBy);

            $this->db->limit($iPageSize,$iRecordStartFrom);
            $result = $this->db->get();
            //echo $this->db->last_query();exit;
            if($result->num_rows()){
                    return $result->result_array();
            }else return 0;
    }

    public function getCityCountByRegion($region_id = 0){
        $this->db->select("region_id",FALSE);
        if($region_id){
            $this->db->where('region_id',$region_id);
        }
        $this->db->where('status',"1");
        $this->db->order_by("city_name","ASC");
        $result = $this->db->get('city');
        return $result->num_rows();
    }
}

?>