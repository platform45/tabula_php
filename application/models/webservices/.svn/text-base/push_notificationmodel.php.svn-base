<?php
/*
* Programmer Name:AD
* Purpose: Model for controlling database interactions regarding the user CRUD operations.
* Date: 14 Aug 2015
* Dependency: None
*/
class Push_notificationmodel extends CI_Model
{
        /*
        * Purpose: Constructor.
        * Date: 02-Sept-2015
        * Input Parameter: None
        *  Output Parameter: None
        */
        public function __construct( )
        {
            parent::__construct();
            $this->load->model('webservices/membersmodel','membersmodel',TRUE);
            $this->load->model('webservices/wallmodel','wallmodel',TRUE);
            $this->load->model('webservices/geozonemodel','geozonemodel',TRUE);
        }
        
        /**
        * This method can be used to insert or update record
        * @param type $action
        * @param type $arrData
        * @param type $edit_id
        * @return type int
        */
        public function action($action,$arrData = array(),$edit_id =0)
        {
            switch($action){
                case 'insert':
                    $this->db->insert('jinx_pushnotification',$arrData);
                    return $this->db->insert_id();
                    break;
                case 'update':
                    $this->db->where('push_message_id',$edit_id);
                    $this->db->update('jinx_pushnotification',$arrData);
                    return $edit_id;
                    break;
            }
        }
        
}
?>