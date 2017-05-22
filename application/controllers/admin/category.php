<?php
/*
* Programmer Name:SK
* Purpose:Role Controller
* Date:19 Dec 2014
* Dependency: categorymodel.php
*/
class Category extends CI_Controller
{
        /*
        * Purpose: Constructor.
        * Date: 19 Dec 2014
        * Input Parameter: None
        *  Output Parameter: None
        */
	function __construct()
        {
            parent::__construct();

            $this->load->model('admin/categorymodel','categorymodel',TRUE);
            
        }
           
        /*
        * Purpose: To Load role
        * Date: 19 Dec 2014
        * Input Parameter: None
        * Output Parameter: None
        */
	public function index()
	{
            if($this->session->userdata('user_id'))
            {
                $data['categoryData'] = $this->categorymodel->getData();
                
                $this->template->view('category',$data);
            }
            else
            {
                redirect('admin', 'refresh');
            }
	}
        public function category_count()
        {
            $cnt=$this->categorymodel->category_count();
            echo $cnt;
        }
        
        
        public function addedit($edit_id = 0)
	{
            if($this->session->userdata('user_id'))
            {
                $data = array();
                $data['edit_id'] = $edit_id;
                $formData = array(
                    'txttitle'=>'',
                    'txtdesc'=>'',
                    'channel_id'=>'',
                    'category_icon'=>''
                );
                
                if(empty($_POST))
                {
                    if($edit_id)
                    {
                        $editData = $this->categorymodel->getData($edit_id);
                        if($editData){
                            $formData = array(
                                'txttitle'=>$editData->category_name,
                                'txtdesc'=>$editData->category_desc,
                                'channel_id'=>$editData->channel_id,
                                'category_icon'=>$editData->category_icon,
                            );
                        }
                    }
                    $data['getChannel'] = $this->categorymodel->getCategoryData();
                    $data['formData']=$formData;    
                    $this->template->view('addcategory',$data);
                }
                else{
                    // process posted data
                    $edit_id = $this->input->post('edit_id');
                    if($edit_id){
                            
                            
                            $update_data = array(
                                 'category_name'=> mysql_real_escape_string($this->input->post('txttitle')),
                                 'channel_id'=> mysql_real_escape_string($this->input->post('channel_id')),
                                 'category_desc'=> $this->input->post('txtdesc'),
                                 'modiefied_on'=> date("Y-m-d h:i:s"),
                                
                            );
                        $icon = "";
                        if ($_FILES['icon']['name'] != '') 
                        {
                            $upload_data = $this->upload_icon_image();
                            if(array_key_exists('error', $upload_data))
                            {
                                $this->session->set_userdata('toast_error_message',$upload_data['error']);
                                redirect('admin/channel/addedit','refresh');
                            }
                            else
                            {    
                                    $update_data['category_icon']= $upload_data['file_name']; 
                            }
                        }
                            
                        $result = $this->categorymodel->action('update',$update_data,$edit_id);
                        if($result){
                           
                                $this->session->set_userdata('toast_message','Record updated successfully');
                                redirect('admin/category');
                            
                                
                        }
                        else{
                            $this->session->set_userdata('toast_message','Unable to add record');
                        }
                    }
                    else{
                        $maxsequnce = $this->categorymodel->getMaxSeq();
                        
                        $insert_data = array(
                            'category_name'=> mysql_real_escape_string($this->input->post('txttitle')),
                            'channel_id'=> mysql_real_escape_string($this->input->post('channel_id')),
                            'category_desc'=> $this->input->post('txtdesc'),
                            'category_status'=> 1,
                            'category_sequence'=> $maxsequnce,
                            'created_on'=> date("Y-m-d h:i:s"),
                            'is_delete'=> 0
                        );
                        $icon = "";
                        if ($_FILES['icon']['name'] != '') 
                        {
                            $upload_data = $this->upload_icon_image();
                            if(array_key_exists('error', $upload_data))
                            {
                                $this->session->set_userdata('toast_error_message',$upload_data['error']);
                                redirect('admin/channel/addedit','refresh');
                            }
                            else
                            {    
                                    $insert_data['category_icon']= $upload_data['file_name']; 
                            }
                        }
                        
                        $result = $this->categorymodel->action('insert',$insert_data);
                        if($result){
                            $this->session->set_userdata('toast_message','Record added successfully');
                           
                            //redirect('admin/channel_crop');
                            redirect('admin/category');
                        }
                        else{
                            $this->session->set_userdata('toast_message','Unable to add record');
                        }
                    }
                }
            }
            else
            {
                redirect('admin', 'refresh');
            }
        }        
        
        function stripJunk($string){
            $string = str_replace(" ", "-", trim($string));
            $string = preg_replace("/[^a-zA-Z0-9-.]/", "", $string);
            $string = strtolower($string);
            return $string;
        }
        /*
        * Purpose: Method to upload brand image to the server.
        * Date: 12 Oct 2014
        * Input Parameter: None
        *  Output Parameter: 
        *  TRUE : if image upload succeeds.
        *  FALSE : if image upload fails.
        */
	    public function upload_icon_image()
	{
            if (!file_exists(CATEGORY_ICON_PATH)) {
                mkdir(CATEGORY_ICON_PATH, 0700,true);
            }
            $file_name= $this->stripJunk($_FILES['icon']['name']);//preg_replace('/[^a-zA-Z0-9_.]/s', '', $_FILES['image']['name']);
            
		$config =  array(
                  'upload_path'     => CATEGORY_ICON_PATH,
                  'allowed_types'   => "jpg|png|jpeg",
                  'overwrite'       => FALSE,
                  'max_size'        => MAX_UPOAD_IMAGE_SIZE,
                  'max_height'      => "2160",
                  'max_width'       => "4096",
                  'file_name'        => $file_name
                );
                $this->load->library('upload', $config);
                
                if($this->upload->do_upload('icon'))
                {
                    $upload_data = $this->upload->data();
                    $data = array('file_name' => $upload_data['file_name']);
                    return $data;
                }
                else
                {
                    $error = array('error' => $this->upload->display_errors());
                    return $error;
                }
        }
        
        // delete brand image 
        public function delete_category($cat_id = 0)
        {
            $update_array = array(
                'is_delete'=>1
            );
            $this->categorymodel->action('update',$update_array,$cat_id);
            $this->session->set_userdata('toast_message','Record deleted successfully');
            redirect('admin/category');
        }
        
        public function update_category_status()
        {
            $cat_id = $this->input->post('cat_id');
            $this->categorymodel->update_category_status($cat_id);
        }
        
        public function change_sequence($move='up',$mnu_id = 0)
        {
            $this->categorymodel->change_sequence($mnu_id,$move);
            redirect('admin/category');
        }
          public function check_title_exists($id = FALSE)
        {
           $channel_id = $this->input->post('channel_id');
           if($id === FALSE)
                {
                       
                        $title = $this->input->post('title');
                        if($title!='')
                        {
                                $url_exists = $this->categorymodel->check_title_exists($title,$channel_id);
                                if($url_exists && (strcmp($url_exists,$title)!=0))
                                        echo json_encode(TRUE);
                                else
                                        echo json_encode(FALSE);
                        }
                        else
                                echo json_encode(TRUE);
                }
                else
                {
                        
                    $title = $this->input->post('title');
                        if($title!='')
                        {
                                $url_exists = $this->categorymodel->check_title_exists($title,$id,$channel_id);
                                if($url_exists && (strcmp($url_exists,$title)!=0))
                                        echo json_encode(TRUE);
                                else
                                        echo json_encode(FALSE);
                        }
                        else
                                echo json_encode(TRUE);
                }
        }
        
         public function check_having_video(){
            
            $category_id = $this->input->post("category_id");
            $this->db->select("vid_id");
            $this->db->where("is_deleted",0);
            $this->db->where("category_id",$category_id);
            $result =$this->db->get("video");
            
            if($result->num_rows() > 0)
            {
                echo 0;
            }
            else
            {
                echo 1;
            }
                
        }
}
?>