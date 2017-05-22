<?php
class Categorymodel extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function getData($edit_id = 0)
  {
    $this->db->select("`cat_id`,cat.category_icon, cat.`channel_id`,chan.channel_title ,`category_name`, `category_desc`, `category_status`, `is_delete`, cat.`created_on`, cat.`modiefied_on`",FALSE);
    $this->db->from('category');
    $this->db->join('channel chan','cat.channel_id = chan.channel_id','left');
    if($edit_id){
      $this->db->where('cat_id',$edit_id);
    }
    $this->db->where(
      array(
        'is_delete' => 0
        ));
    $result = $this->db->get();
    if($result->num_rows()){
      if($edit_id)
        return $result->row();
      else
        return $result->result_array();
    }
    else
      return 0;
  }

  public function getCategoryData($edit_id = 0)
  {
    $this->db->select("`channel_id`, `channel_title`, `channel_oneliner`, `channel_image`, `channel_sequence`, `channel_status`, `is_deleted`, `created_on`, `modified_on`",FALSE);

    $this->db->where(
      array(
        'is_deleted' => 0,
        'channel_status' => 1
        ));

    $result = $this->db->get('channel');
    if($result->num_rows()){            
      return $result->result_array();
    }
    else
      return 0;
  }

  public function category_count()
  {
    $this->db->select("`cat_id`, `channel_id`, `category_name`, `category_desc`, `category_status`, `is_delete`, `created_on`, `modiefied_on`",FALSE);

    $this->db->where(
      array(
        'is_delete' => 0,
        'category_status' => 1
        ));
    
    $result = $this->db->get('category');
    return $result->num_rows();

  }

  public function action($action,$arrData = array(),$edit_id =0)
  {
    switch($action){
      case 'insert':
      $this->db->insert('category',$arrData);
      return $this->db->insert_id();
      break;
      case 'update':
      $this->db->where('cat_id',$edit_id);
      $this->db->update('category',$arrData);
      return $edit_id;
      break;
      case 'delete':
      break;
    }
  }

  public function update_category_status($channel_id = 0)
  {
    $this->db->select('category_status');
    $this->db->from('category');
    $this->db->where('cat_id',$channel_id);
    $query = $this->db->get();
    if($query->num_rows() > 0){
      $query = $query->row_array();
      if($query['category_status'] == 1)
      {
        $data = array(
          'category_status' => 0
          );
      }
      else
      {
        $data = array(
          'category_status' => 1
          );
      }
      $this->db->where('cat_id',$channel_id);
      $this->db->update('category',$data);

    }
  }
  
  public function getMaxSeq(){
    $this->db->select_max('category_sequence');
    $this->db->from('category');
    $this->db->where('is_delete','0');
    $query = $this->db->get();
    if($query->num_rows() > 0)
    {
      $query = $query->row();
      $query = $query->category_sequence;
      return $query + 1;
    }
    else
    {
      return 1;
    }

  }
  
  public function change_sequence($faqid = 0,$change_to = 'up')
  {
                // get sequence of current menu
    $curr_faq = 0;
    $this->db->select('channel_id,channel_sequence');
    $this->db->where('channel_id',$faqid);
    $result = $this->db->get('channel');
    if($result->num_rows() > 0)
    {
      $curr_faq = $result->row();
    }


    $other_menu = 0;
    $this->db->select('channel_id,channel_sequence');
    if($change_to == 'up')
    {
      $this->db->where('channel_sequence <',$curr_faq->channel_sequence);

      $this->db->order_by('channel_sequence','DESC');
    }
    else{
      $this->db->where('channel_sequence >',$curr_faq->channel_sequence);

      $this->db->order_by('channel_sequence','ASC');
    }
    $this->db->where('is_deleted',0);
    $this->db->limit(1);

    $result = $this->db->get('channel');
    if($result->num_rows() > 0)
    {
      $other_menu = $result->row();
    }
    else
      return 'NA';

    if($other_menu){
                        // update sequence of current menu
      $update_seq = ($other_menu->channel_sequence);
      $update_data = array('channel_sequence'=>$update_seq);
      $this->db->where('channel_id',$curr_faq->channel_id);

      $this->db->update('channel',$update_data);

                        // update sequence of other menu
      $update_seq = ($curr_faq->channel_sequence);
      $update_data = array('channel_sequence'=>$update_seq);
      $this->db->where('channel_id',$other_menu->channel_id);
      $this->db->update('channel',$update_data);

      return 'DONE';
    }

  }

  public function check_title_exists($link,$id = FALSE,$channel_id)
  {
    if($id === FALSE)
    {
      $this->db->select('category_name');
      $this->db->from('category');
      $this->db->where('category_name',$link);
      $this->db->where(array('is_delete' => 0));
      $this->db->where(array('channel_id' => $channel_id));
      $this->db->limit(1);
      $query = $this->db->get();
      if($query->num_rows() > 0)
        return false;
      else
        return true;
    }
    else
    {
      $this->db->select('category_name');
      $this->db->from('category');
      $this->db->where('category_name',$link);
      $this->db->where(array('is_delete' => 0));
      $this->db->where(array('channel_id' => $channel_id));
      $this->db->where('cat_id <> ',$id);
      $this->db->limit(1);
      $query = $this->db->get();
      if($query->num_rows() > 0)
        return false;
      else
        return true;
    }
  }

}
?>