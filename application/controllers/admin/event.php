<?php
/*
* Programmer Name:SK
* Purpose:Role Controller
* Date:19 Dec 2014
* Dependency: eventmodel.php
*/
class Event extends CI_Controller
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

            $this->load->model('admin/eventmodel','eventmodel',TRUE);
            
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
                $data['eventData'] = $this->eventmodel->getData();
                $this->template->view('event',$data);
            }
            else
            {
                redirect('admin', 'refresh');
            }
	}
        public function event_count()
        {
            $cnt=$this->eventmodel->event_count();
            echo $cnt;
        }
        
         public function event_state_count()
         {
             $cnt=$this->eventmodel->event_state_count();
             echo $cnt;
         }
         
         public function check_title_exists($id = FALSE)
        {
                if($id === FALSE)
                {
                        $title = $this->input->post('title');
                    
                        if($title!='')
                        {
                                $url_exists = $this->eventmodel->check_title_exists($title);
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
                                $url_exists = $this->eventmodel->check_title_exists($title,$id);
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
                    'image'=>'',
                    'txtdesc'=>'',
                    'txtbrief'=>'',
                    'txtdate'=>'',
                    'metakeywords'=>'',
                    'metatag'=>'',
                    'txttodate'=>'',
                    'txtlocation'=>''
                );
                
                if(empty($_POST))
                {
                    if($edit_id)
                    {
                        $editData = $this->eventmodel->getData($edit_id);
                        if($editData){
                            $formData = array(
                               'txttitle'=>$editData->ne_title,
                               'image'=>$editData->ne_image,
                               'txtdesc'=>$editData->ne_desc,
                                'txtbrief'=>$editData->ne_brief,
                                'txtdate'=>date('m/d/Y h:i A', strtotime(($editData->ne_date))),
                                'txttodate'=>date('m/d/Y h:i A', strtotime(($editData->ne_to_date))),
                                'metakeywords'=>$editData->ne_metakeywords,
                                'metatag'=>$editData->ne_metatag,
                                'txtlocation'=>$editData->e_location
                            );
                            
                           // print_r($formData);
                        }
                    }
                    $data['formData']=$formData;    
                    $this->template->view('addevent',$data);
                }
                else{
                    // process posted data
                    $edit_id = $this->input->post('edit_id');
                    if($edit_id){
                        $file_name = "";
                        if ($_FILES['image']['name'] != '') 
                        {
                            $upload_data = $this->upload_event_image();
                            if(array_key_exists('error', $upload_data))
                            {
                                $this->session->set_userdata('toast_error_message',$upload_data['error']);
                                redirect('event/addedit','refresh');
                            }
                            else
                            {                        
                                $file_name = $upload_data['file_name'];
                                $update_data = array(
                                    'ne_date'=>date('Y-m-d H:i:s', strtotime(($this->input->post('txtdate')))),
                                    'ne_to_date'=>date('Y-m-d H:i:s', strtotime(($this->input->post('txttodate')))),
                            
                                    'ne_title'=> mysql_real_escape_string($this->input->post('txttitle')),
                                    'ne_brief'=> mysql_real_escape_string($this->input->post('txtbrief')),
                                    'ne_metakeywords'=> mysql_real_escape_string($this->input->post('metakeywords')),
                                    'ne_metatag'=> mysql_real_escape_string($this->input->post('metatag')),
                                    'ne_desc'=> $this->input->post('en_txtContent'),
                                    'ne_image'=>$file_name,
                                    'e_location'=>$this->input->post('txtlocation')
                                    );
                                
                            }
                        }
                        else
                        {
                            $update_data = array(
                                'ne_date'=>date('Y-m-d H:i:s', strtotime(($this->input->post('txtdate')))),
                                'ne_to_date'=>date('Y-m-d H:i:s', strtotime(($this->input->post('txttodate')))),
                                'ne_title'=> mysql_real_escape_string($this->input->post('txttitle')),
                                'ne_brief'=> mysql_real_escape_string($this->input->post('txtbrief')),
                                'ne_metakeywords'=> mysql_real_escape_string($this->input->post('metakeywords')),
                                'ne_metatag'=> mysql_real_escape_string($this->input->post('metatag')),
                                'ne_desc'=> $this->input->post('en_txtContent'),
                                 'e_location'=>$this->input->post('txtlocation')
                            );
                        }
                        
                        //Query Execute 
                        $result = $this->eventmodel->action('update',$update_data,$edit_id);
                        
                        if($result){
                            if(!empty($file_name)){
                                // delete old image
                                $old_image = $this->input->post('old_img');
                                $path = $old_image;
                                        $ext = pathinfo($path, PATHINFO_EXTENSION);
                                        $thumb_img = basename($path, ".".$ext); 
                                        $thumb_img = $thumb_img."_thumb.".$ext;
                                if(file_exists(NEWS_IMAGE_PATH . $old_image))        
                                {
                                    @unlink(NEWS_IMAGE_PATH . $old_image);
                                    @unlink(NEWS_IMAGE_PATH . $thumb_img);
                                }
                                
                                // end
                                $this->session->set_userdata('toast_message','Record updated successfully');
                                $this->session->set_userdata('uploaded_img',$file_name);
                                $this->session->set_userdata('redirect_to','admin/event');
                                redirect('admin/news_crop');
                            }
                            else
                            {
                                $this->session->set_userdata('toast_message','Record updated successfully');
                                redirect('admin/event');
                            }
                                
                        }
                        else{
                            $this->session->set_userdata('toast_message','Unable to add record');
                            redirect('admin/event');
                            
                        }
                    }
                    else{
                        $maxsequnce = $this->eventmodel->getMaxSeq();
                        
                        $insert_data = array(
                            'ne_date'=>date('Y-m-d H:i:s', strtotime(($this->input->post('txtdate')))),
                            'ne_to_date'=>date('Y-m-d H:i:s', strtotime(($this->input->post('txttodate')))),
                            
                            'ne_title'=> mysql_real_escape_string($this->input->post('txttitle')),
                            'ne_brief'=> mysql_real_escape_string($this->input->post('txtbrief')),
                            'ne_metakeywords'=> mysql_real_escape_string($this->input->post('metakeywords')),
                            'ne_metatag'=> mysql_real_escape_string($this->input->post('metatag')),
                            'e_location'=>$this->input->post('txtlocation'),
                            'ne_desc'=> $this->input->post('en_txtContent'),
                            'ne_status'=> 1,
                            'ne_type'=> 2,
                            'ne_sequence'=> $maxsequnce,
                            'is_deleted'=> 0
                        );
                        if ($_FILES['image']['name'] != '') 
                        {
                            $upload_data = $this->upload_event_image();
                            if(array_key_exists('error', $upload_data))
                            {
                                $this->session->set_userdata('toast_error_message',$upload_data['error']);
                                redirect('event/addedit','refresh');
                            }
                            else
                            {                        
                                $insert_data['ne_image'] = $upload_data['file_name'];
                                $file_name = $upload_data['file_name'];
                            }
                        }
                        
                        $result = $this->eventmodel->action('insert',$insert_data);
                        if($result){
                            $this->session->set_userdata('toast_message','Record added successfully');
                            //$this->session->set_userdata('toast_message','Record updated successfully');
                            $this->session->set_userdata('uploaded_img',$file_name);
                            $this->session->set_userdata('redirect_to','admin/event');
                            redirect('admin/news_crop');
                            //redirect('event');
                        }
                        else{
                            $this->session->set_userdata('toast_message','Unable to add record');
                            redirect('admin/event');
                            
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
        * Purpose: Method to upload event image to the server.
        * Date: 12 Oct 2014
        * Input Parameter: None
        *  Output Parameter: 
        *  TRUE : if image upload succeeds.
        *  FALSE : if image upload fails.
        */
	public function upload_event_image()
	{
            if (!file_exists(NEWS_IMAGE_PATH)) {
                mkdir(NEWS_IMAGE_PATH, 0700,true);
            }
            $file_name= $this->stripJunk($_FILES['image']['name']);//preg_replace('/[^a-zA-Z0-9_.]/s', '', $_FILES['image']['name']);
            
		$config =  array(
                  'upload_path'     => NEWS_IMAGE_PATH,
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
        
        // delete event image 
        public function delete_event($ne_id = 0)
        {
            $update_array = array(
                'is_deleted'=>1
            );
            $this->eventmodel->action('update',$update_array,$ne_id);
            $this->session->set_userdata('toast_message','Record deleted successfully');
            redirect('admin/event');
        }
        
        public function update_status()
        {
            $sli_id = $this->input->post('ne_id');
            $this->eventmodel->update_status($sli_id);
        }
        
        public function change_sequence($move='up',$mnu_id = 0)
        {
            $this->eventmodel->change_sequence($mnu_id,$move);
            redirect('admin/event');
        }
}
?>