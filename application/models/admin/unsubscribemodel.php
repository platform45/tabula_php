<?php
class Unsubscribemodel extends CI_Model
{   
    public function __construct()
    {
            parent::__construct();
    }
    public function getData($sub_id,$email){
      
		$aUpdate1 = array(
            "sub_unsub_status" => 0
        );
        $this->db->where("sub_email",$email);
        $this->db->update("subscribermst",$aUpdate1);
		
        $sReturn = "You are successfully unsubscribed from the newsletter. You will no longer be able to receive the mails from CTS.";
        return $sReturn;
    }
	
	public function getEmail($sub_id){
		$this->db->select('sub_id,sub_email,sub_unsub_status', FALSE);
		$this->db->where("sub_id",$sub_id);
        $this->db->where(array('is_deleted' => 0));
        $result = $this->db->get('subscribermst');
	
        if ($result->num_rows()) {
            return $result->row();
        }
        else
            return 0;
	}

    public function unsubscribe($email)
    {   
        $aUpdate1 = array(
            "sub_unsub_status" => 0
        );
        $this->db->where("sub_email",$email->sub_email);
        $this->db->update("subscribermst",$aUpdate1);
         $sReturn = "You are successfully unsubscribed from the newsletter. You will no longer be able to receive the mails from CTS.";
        echo $sReturn;
    }
}
?>