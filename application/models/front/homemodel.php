<?php
class Homemodel extends CI_Model
{
    /*
    * Purpose: Constructor.
    * Input Parameter: None
    *  Output Parameter: None
    */
	public function __construct( )
    {
        parent::__construct();
    }
    
    // get menu for cms
    public function getMenus()
    {
        $this->db->select("`mnu_menuid`,mnu_sequence, `mnu_menu_name`, `mnu_url`, `mnu_status`, `mnu_type`, `mnu_sequence`");
        $this->db->where("mnu_status",1);
         $this->db->order_by("mnu_sequence", "ASC");
        $result = $this->db->get('menumst');
        if ($result->num_rows() > 0) {
            return $result->result_array();
        } else {
            return 0;
        }
    }
    
    
    //get slider
    public function getSliders()
    {
        $this->db->select("sli_id,sli_title,sli_image,sli_url,sli_status",FALSE);
        
        $this->db->where(
                    array(
                        'is_deleted' => 0,
                        'sli_status' => 1,
                        'sli_type' => "Slider"
                    ));       
        $this->db->order_by('sli_sequence','ASC');
        $result = $this->db->get('slidermst');
        if($result->num_rows()){            
         return $result->result_array();
        }
        else
            return 0;
    }

    public function getData($booking_id)
    {
        $sql = "
        SELECT  r.booking_id, r.booking_number, 
        DATE_FORMAT(r.booking_from_time, '%d-%m-%Y') as booking_date,
        DATE_FORMAT(r.booking_from_time, '%H:%i') as booking_time,
        r.number_of_guest,
        adr.time_slot_id,
        GROUP_CONCAT(adr.table_id) as table_ids,
		is_notify,last_minute_from_time,last_minute_to_time
        FROM tab_booking_request r 
        JOIN tab_admin_booking_request adr ON r.booking_id = adr.booking_id
        WHERE r.booking_id = $booking_id
        ";

        $query = $this->db->query( $sql );
        return ( $query->num_rows() > 0 )  ? $query->row() : array();
    }

}
?>