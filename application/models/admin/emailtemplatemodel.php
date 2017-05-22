<?php
/* * ****************** PAGE DETAILS ******************* *
 * @Programmer  : Pankaj Swami
 * @Maintainer  : Pankaj Swami
 * @Created     : 20 Aug 2015
 * @Modified    : 
 * @Description : EmailTemplate model.
 * ****************************************************** */

Class EmailTemplateModel extends CI_Model {
    
    function __construct()
    {
         parent::__construct();         
    }
    
    
    /*********************************************************
     * EmailTemplate MODEL's DEFAULT FUNCTION USE TO SHOW EmailTemplate LISTING
     **********************/
    function index() {
        
        //--- SELECT FIELDS FROM CHEF TABLE ---//
        $this->db->select('email_id,email_name,email_subject,email_from,email_body',FALSE);
        $this->db->order_by("email_id","DESC");
        $result = $this->db->get('emailtemplatemst');
        if($result->num_rows()){
                return $result->result_array();
        }
        else
            return 0;
    }

    /************************************************************
     * CHEF MODEL's ADD/UPDATE ACTION PERFORM USING THIS FUNCTION
     **********************/
    public function action($action,$arrData = array(),$edit_id =0)
    {
        switch($action){
            case 'insert':
                $this->db->insert('email_template',$arrData);
                return $this->db->insert_id();
                break;
            case 'update':
                $this->db->where('emt_id',$edit_id);
                $this->db->update('email_template',$arrData);
                return $edit_id;
                break;
            case 'delete':
                break;
        }
    }
    
    /****************************************
     * CHEF DETAIL ACCESS USING THIS FUNCTION
     **********************/
    public function getData($iUserID = 0)
    {
        $this->db->select('*',FALSE);
        $this->db->from('emailtemplatemst');
        $this->db->where('email_id',$iUserID);
        $this->db->limit(1);
        $result = $this->db->get();
        
        if($result->num_rows()){
                return $result->result_array();
        }
        else
            return 0;
    }
    
    /**************************************
     * CHEF OPEN CLOSE DAYS AND TIME DETAIL
     **********************/
    public function getOpenCloseTime($iUserID = 0)
    {
        $this->db->select('*',FALSE);
        $this->db->from('open_close_time');
        $this->db->where('emt_id',$iUserID);
        $result = $this->db->get();
        
        if($result->num_rows()){
           return $result->result_array();
        }else return 0;
    }
    
    /****************************************
     * CHEF DETAIL UPDATE USING THIS FUNCTION
     * PARAMETER: 
     * $iUserID- chef id, $aPostData - form post data
     * $aChefDetail - current available details.
     **********************/
    public function UpdateDetail($iUserID = 0,$aPostData)
    {
        //--- UDPATE USER TABLE ---//
        $this->db->where('email_id', $iUserID);
        $this->db->update('emailtemplatemst', $aPostData);
         #print_r($aOpenDaysID);exit;
        $this->session->set_userdata('toast_message','Record updated successfully');
        return true;
    }
    
    
    /****************************************
     * CHEF ADD DETAIL USING THIS FUNCTION
     * PARAMETER: 
     * $aPostData - form post data
     * $aPostDataForOpenClose - Open days and time.
     **********************/
    public function AddDetail($aPostData)
    {
        $sTableName = "emailtemplatemst";

        //---- INSERT USER IN DATABASE ----//
        $this->db->insert($sTableName, $aPostData); 

        //---- RETURN LAST INSERT ID ----//
        $iLastInsertID = $this->db->insert_id();

        //---- LAST INSERT ID ----//
        if($iLastInsertID > 0){
            return true;
        }//End last insert id if condition            
        return false;
    }
}
?>