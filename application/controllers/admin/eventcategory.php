<?php
/*
* Programmer Name:SK
* Purpose:Role Controller
* Date:19 Dec 2014
* Dependency: eventcategorymodel.php
*/
class Eventcategory extends CI_Controller
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

            $this->load->model('eventcategorymodel','',TRUE);
            
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
                $data['eventcategoryData'] = $this->eventcategorymodel->getData();
                $this->template->view('eventcategory',$data);
            }
            else
            {
                redirect('admin', 'refresh');
            }
	}
        public function eventcategory_count()
        {
            $cnt=$this->eventcategorymodel->eventcategory_count();
            echo $cnt;
        }
        
         public function eventcategory_state_count()
         {
             $cnt=$this->eventcategorymodel->eventcategory_state_count();
             echo $cnt;
         }
         
        public function check_title_exists($id = FALSE)
        {
                if($id === FALSE)
                {
                        $title = $this->input->post('title');
                        if($title!='')
                        {
                                $url_exists = $this->eventcategorymodel->check_title_exists($title);
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
                                $url_exists = $this->eventcategorymodel->check_title_exists($title,$id);
                                if($url_exists && (strcmp($url_exists,$title)!=0))
                                        echo json_encode(TRUE);
                                else
                                        echo json_encode(FALSE);
                        }
                        else
                                echo json_encode(TRUE);
                }
        }
         
         
         
         
        public function addedit($edit_id = 0)
	{
            if($this->session->userdata('user_id'))
            {
                $data = array();
                $data['edit_id'] = $edit_id;
                $formData = array(
                    'txttitle'=>'',
                    'image'=>''
                );
                
                if(empty($_POST))
                {
                    if($edit_id)
                    {
                        $editData = $this->eventcategorymodel->getData($edit_id);
                        if($editData){
                            $formData = array(
                                'txttitle'=>$editData->category_title,
                               'image'=>$editData->category_title
                            );
                        }
                    }
                    $data['formData']=$formData;    
                    $this->template->view('addeventcategory',$data);
                }
                else{
                    // process posted data
                    $edit_id = $this->input->post('edit_id');
                    if($edit_id){
                        $file_name = "";
                        if ($_FILES['image']['name'] != '') 
                        {
                            $upload_data = $this->upload_eventcategory_image();
                            if(array_key_exists('error', $upload_data))
                            {
                                $this->session->set_userdata('toast_error_message',$upload_data['error']);
                                redirect('eventcategory/addedit','refresh');
                            }
                            else
                            {                        
                                $update_data = array(
                                    'category_title'=> mysql_real_escape_string($this->input->post('txttitle'))
                                    
                                
                                    );
                                $file_name = $upload_data['file_name'];
                            }
                        }
                        else
                        {
                            $update_data = array(
                                'category_title'=> mysql_real_escape_string($this->input->post('txttitle'))
                                
                            );
                        }
                        $result = $this->eventcategorymodel->action('update',$update_data,$edit_id);
                        if($result){
                            if(!empty($file_name)){
                                // delete old image
                                $old_image = $this->input->post('old_img');
                                $path = $old_image;
                                        $ext = pathinfo($path, PATHINFO_EXTENSION);
                                        $thumb_img = basename($path, ".".$ext); 
                                        $thumb_img = $thumb_img."_thumb.".$ext;
                                if(file_exists(BRAND_IMAGE_PATH . $old_image))        
                                {
                                    @unlink(BRAND_IMAGE_PATH . $old_image);
                                    @unlink(BRAND_IMAGE_PATH . $thumb_img);
                                }
                                
                                // end
                                $this->session->set_userdata('toast_message','Record updated successfully');
                                $this->session->set_userdata('uploaded_img',$file_name);
                                $this->session->set_userdata('redirect_to','eventcategory');
                                redirect('eventcategory');
                            }
                            else
                            {
                                $this->session->set_userdata('toast_message','Record updated successfully');
                                redirect('eventcategory');
                            }
                                
                        }
                        else{
                            $this->session->set_userdata('toast_message','Unable to add record');
                        }
                    }
                    else{
                        $maxsequnce = $this->eventcategorymodel->getMaxSeq();
                        
                        $insert_data = array(
                            'category_title'=> mysql_real_escape_string($this->input->post('txttitle')),
                            'category_status'=> 1,
                            'category_sequence'=> $maxsequnce,
                            'is_deleted'=> 0
                        );
                        if ($_FILES['image']['name'] != '') 
                        {
                            $upload_data = $this->upload_eventcategory_image();
                            if(array_key_exists('error', $upload_data))
                            {
                                $this->session->set_userdata('toast_error_message',$upload_data['error']);
                                redirect('eventcategory/addedit','refresh');
                            }
                            else
                            {                        
                                $insert_data['sli_image'] = $upload_data['file_name'];
                                $file_name = $upload_data['file_name'];
                            }
                        }
                        
                        $result = $this->eventcategorymodel->action('insert',$insert_data);
                        if($result){
                            $this->session->set_userdata('toast_message','Record added successfully');
                            //$this->session->set_userdata('toast_message','Record updated successfully');
                            $this->session->set_userdata('uploaded_img',$file_name);
                            $this->session->set_userdata('redirect_to','eventcategory');
                            redirect('eventcategory');
                            //redirect('eventcategory');
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
        * Purpose: Method to upload eventcategory image to the server.
        * Date: 12 Oct 2014
        * Input Parameter: None
        *  Output Parameter: 
        *  TRUE : if image upload succeeds.
        *  FALSE : if image upload fails.
        */
	public function upload_eventcategory_image()
	{
            if (!file_exists(BRAND_IMAGE_PATH)) {
                mkdir(BRAND_IMAGE_PATH, 0700,true);
            }
            $file_name= $this->stripJunk($_FILES['image']['name']);//preg_replace('/[^a-zA-Z0-9_.]/s', '', $_FILES['image']['name']);
            
		$config =  array(
                  'upload_path'     => BRAND_IMAGE_PATH,
                  'allowed_types'   => "jpg|png|jpeg",
                  'overwrite'       => FALSE,
                  'max_size'        => MAX_UPOAD_IMAGE_SIZE,
                  'max_height'      => "2160",
                  'max_width'       => "4096",
                  'file_name'        => $file_name
                );
                $this->load->library('upload', $config);
                if($this->upload->do_upload('image'))
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
        
        // delete eventcategory image 
        public function delete_eventcategory($category_id = 0)
        {
            $update_array = array(
                'is_deleted'=>1
            );
            $this->eventcategorymodel->action('update',$update_array,$category_id);
            $this->session->set_userdata('toast_message','Record deleted successfully');
            redirect('eventcategory');
        }
        
        public function update_status()
        {
            $sli_id = $this->input->post('category_id');
            $this->eventcategorymodel->update_status($sli_id);
        }
        
        public function change_sequence($move='up',$mnu_id = 0)
        {
            $this->eventcategorymodel->change_sequence($mnu_id,$move);
            redirect('eventcategory');
        }
}
?>