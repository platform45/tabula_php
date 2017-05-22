<?php
/******************** PAGE DETAILS ********************/
/* @Programmer  : TUSHAR KOCHAR
 * @Maintainer  : TUSHAR KOCHAR
 * @Created     : 5 Aug 2016
 * @Modified    : 
 * @Description : This is channel model which is used
 * to show top 10 channels as well as all channels.
********************************************************/
class Channel_model extends CI_Model
{
            
    public function __construct()
    {
            parent::__construct();
    }
    
    public function channel(){
        
       
        //---- TABLE NAME ---//
        $sTableName = 'channel as chan';
        
        //---- WHERE CLAUSE ----//
        $aWhereClause = array("chan.channel_status ="=>1,"chan.is_deleted ="=>0);        
            
        $this->db->select("chan.channel_id,chan.channel_title,chan.created_on,chan.channel_icon,chan.channel_oneliner,chan.channel_image,chan.channel_icon,chan.channel_share_count");
        $this->db->from($sTableName);        
        $this->db->order_by("channel_sequence","ASC");      
        $this->db->where($aWhereClause);
        $aResult = $this->db->get();  
        if ($aResult->num_rows() > 0) {
            return $aResult->result_array();           
        } else {
            return false;
        }
    }
    
     public function channelMaxCount(){
        
    
        //---- TABLE NAME ---//
        $sTableName = 'channel as c';
        
        //---- WHERE CLAUSE ----//
        $aWhereClause = array("c.channel_status ="=>1,"c.is_deleted ="=>0);        
            
        $this->db->select("c.channel_id,c.channel_title,c.channel_oneliner,c.channel_image");
        $this->db->from($sTableName);        
        $this->db->order_by("channel_sequence","ASC");
      
        $this->db->where($aWhereClause);
        $aResult = $this->db->get();
    
        if ($aResult->num_rows() > 0) {
            return $aResult->num_rows();           
        } else {
            return false;
        }
    }
     function channelShareCount($channel_id)
        {
            $this->db->where('channel_id',$channel_id );
            $this->db->set('channel_share_count', 'channel_share_count+1', FALSE);
            $this->db->update('channel');
            return $channel_id;
        }
}
?>