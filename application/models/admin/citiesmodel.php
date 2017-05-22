<?php

class Citiesmodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getData($edit_id = 0, $status = "") {
        $this->db->select("ci.*,r.region_name,c.cou_name", FALSE);
         $this->db->from('city ci');
        if ($edit_id) {
            $this->db->where('city_id', $edit_id);
        }
        if ($status) {
            $this->db->where('status', $status);
        }
        $this->db->join('region r', 'ci.region_id = r.region_id', 'left');
        $this->db->join('country c', 'c.cou_id=r.cou_id', 'left');
        $this->db->order_by("city_name", "ASC");
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

    public function checkCity($where, $edit_id = 0) {
        $this->db->select("city_id, city_name", FALSE);
        if ($edit_id) {
            $this->db->where('city_id != ', $edit_id);
        }
        $this->db->where($where);
        $result = $this->db->get('city');

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
                $this->db->insert('city', $arrData);
                $insert_id = $this->db->insert_id();
                return $insert_id;
                break;
            case 'update':
                $this->db->where('city_id', $edit_id);
                $this->db->update('city', $arrData);
                return $edit_id;
                break;
            case 'delete':
                $this->db->delete('city', array('city_id' => $edit_id));
                break;
        }
    }

    public function getRegionByCountry() {
//        echo $country_id;die;
        $this->db->select("*", FALSE);
        $this->db->where('cou_id', 47);
        $this->db->where('status', "1");

        $this->db->order_by("region_name", "ASC");
        $result = $this->db->get('region');
        if ($result->num_rows()) {
            return $result->result_array();
        }
        else
            return 0;
    }

    public function getCityByRegion($region_id = 0) {
        $this->db->select("*", FALSE);
        if ($region_id) {
            $this->db->where('region_id', $region_id);
        }
        $this->db->where('status', "1");
        $this->db->order_by("city_name", "ASC");
        $result = $this->db->get('city');
        if ($result->num_rows()) {
            return $result->result_array();
        }
        else
            return 0;
    }

    /*
       * Method Name: get_city_name
       * Purpose: Get city name from database
       * params:
       *      input: city id
       *      output: city name
       */
      public function get_city_name( $city )
      {
        $this->db->select("city_name");
        $this->db->from("city");
        $data = array(
                  'city_id' => $city
                );
        $this->db->where( $data );
        $this->db->limit(1);
        $query = $this->db->get();

        return ( $query->num_rows() > 0 ) ? $query->row()->city_name : "";
      }

    /****************************************
     * THIS FUNCTION USE TO GET CITIES COUNT
     * PARAMETER:
     * $aSearchArray: Search parameters available in this array like
     * City name.
     **********************/
    public function CityCount($aSearchArray)
    {
            //---- SEARCH PARAMETERS ----//
            $iSearchByCountryName   = $aSearchArray[0]; /*Country Name*/
            $iSearchByStateName   = $aSearchArray[1]; /*State Name*/
            $iSearchByCityName   = $aSearchArray[2]; /*City Name*/

            $this->db->select("COUNT(city_id) as NumberOfCities",FALSE);
            $this->db->from('city ');

            //---- WHERE CONDITION ----//
            if(!empty($iSearchByCountryName)) $this->db->where("country_id", $iSearchByCountryName);
            if(!empty($iSearchByStateName)) $this->db->where("region_id", $iSearchByStateName);
            if(!empty($iSearchByCityName)) $this->db->where("city_name LIKE '".$iSearchByCityName."%'");

            $result = $this->db->get();

            if($result->num_rows()){
                    return $result->result_array();
            }else return 0;
    }

    /****************************************
     * GET ALL CITIES
     * PARAMETER:
     * $iPageSize: No of records to be display
     * $iRecordStartFrom: Records start from 0,25,50...etc.
     * $aSearchArray: Search parameters avaialble in this array like
     * City name.
     * $aOrderByCondition : Order by array contains order by ASC/DESC and
     * Column name on which order operation perform.
     **********************/
    public function CityListing($iPageSize,$iRecordStartFrom,$aSearchArray,$aOrderByCondition)
    {
            //---- ORDER BY ----//
            $sOrderByColumn         = $aOrderByCondition['colomn_name'];
            $sOrderBy               = $aOrderByCondition['order_by'];


            //---- SEARCH PARAMETERS ----//
            $iSearchByCountryName   = $aSearchArray[0]; /*Country Name*/
            $iSearchByStateName   = $aSearchArray[1]; /*State Name*/
            $iSearchByCityName   = $aSearchArray[2]; /*City Name*/


            $this->db->select("ci.*,r.region_name,c.cou_name",FALSE);
            $this->db->from('city ci');

            $this->db->join('region r', 'ci.region_id = r.region_id', 'left');
            $this->db->join('country c', 'c.cou_id=r.cou_id', 'left');
            $this->db->order_by("city_name", "ASC");

            //---- WHERE CONDITION ----//
            if(!empty($iSearchByCountryName)) $this->db->where("ci.country_id", $iSearchByCountryName);
            if(!empty($iSearchByStateName)) $this->db->where("ci.region_id", $iSearchByStateName);
            if(!empty($iSearchByCityName)) $this->db->where("ci.city_name LIKE '".$iSearchByCityName."%'");


            //---- ORDER BY CONDITION ----//
            $this->db->order_by($sOrderByColumn,$sOrderBy);

            $this->db->limit($iPageSize,$iRecordStartFrom);
            $result = $this->db->get();

            if($result->num_rows()){
                    return $result->result_array();
            }else return 0;
    }

}

?>