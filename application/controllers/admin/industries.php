<?php
/*
* Programmer Name:SK
* Purpose:Role Controller
* Date:19 Dec 2014
* Dependency: industriesmodel.php
*/
class Industries extends CI_Controller
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

            $this->load->model('industriesmodel','',TRUE);
            
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
                $data['industriesData'] = $this->industriesmodel->getData();
                $this->template->view('industries',$data);
            }
            else
            {
                redirect('admin', 'refresh');
            }
	}
        public function industries_count()
        {
            $cnt=$this->industriesmodel->industries_count();
            echo $cnt;
        }
        
         public function industries_state_count()
         {
             $cnt=$this->industriesmodel->industries_state_count();
             echo $cnt;
         }
         
         public function check_title_exists($id = FALSE)
        {
                if($id === FALSE)
                {
                        $title = $this->input->post('title');
                    
                        if($title!='')
                        {
                                $url_exists = $this->industriesmodel->check_title_exists($title);
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
                                $url_exists = $this->industriesmodel->check_title_exists($title,$id);
                                if($url_exists && (strcmp($url_exists,$title)!=0))
                                        echo json_encode(TRUE);
                                else
                                        echo json_encode(FALSE);
                        }
                        else
                                echo json_encode(TRUE);
                }
        }
         
        
        public function check_url_exists($id = FALSE)
        {
                if($id === FALSE)
                {
                        $link = $this->input->post('link');
                        if($link!='')
                        {
                                $url_exists = $this->industriesmodel->check_url_exists($link);
                                if($url_exists && (strcmp($url_exists,$link)!=0))
                                        echo json_encode(TRUE);
                                else
                                        echo json_encode(FALSE);
                        }
                        else
                                echo json_encode(TRUE);
                }
                else
                {
                        $link = $this->input->post('link');
                        if($link!='')
                        {
                                $url_exists = $this->industriesmodel->check_url_exists($link,$id);
                                if($url_exists && (strcmp($url_exists,$link)!=0))
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
                    'ind_id'=>'',
                    'txttitle'=>'',
                    'image'=>'',
                    'en_txtmetadescription'=>'',
                    'en_txtkeywords'=>'',
                    'en_txtpageurl'=>'',
                    'txtdesc'=>'' 
                );
                
                if(empty($_POST))
                {
                    if($edit_id)
                    {
                        $editData = $this->industriesmodel->getData($edit_id);
                        if($editData){
                            $formData = array(
                               'ind_id'=>$editData->ind_id,
                               'txttitle'=>$editData->ind_title,
                               'image'=>$editData->ind_header_img,
                               'en_txtmetadescription'=>$editData->ind_meta_description,
                               'en_txtkeywords'=>$editData->ind_keywords,
                               'en_txtpageurl'=>$editData->ind_url_name,
                               'txtdesc'=>$editData->ind_desc
                            );
                        }
                    }
                    $data['formData']=$formData;    
                    $this->template->view('addindustries',$data);
                }
                else{
                    // process posted data
                    $edit_id = $this->input->post('edit_id');
                    if($edit_id){
                        $file_name = "";
                        if ($_FILES['image']['name'] != '') 
                        {
                            $upload_data = $this->upload_industries_image();
                            if(array_key_exists('error', $upload_data))
                            {
                                $this->session->set_userdata('toast_error_message',$upload_data['error']);
                                redirect('industries/addedit','refresh');
                            }
                            else
                            {                        
                                $update_data = array(
                                    'ind_title'=> mysql_real_escape_string($this->input->post('txttitle')),
                                    'ind_meta_description'=> mysql_real_escape_string($this->input->post('en_txtmetadescription')),
                                    'ind_keywords'=> mysql_real_escape_string($this->input->post('en_txtkeywords')),
                                    'ind_url_name'=> mysql_real_escape_string($this->input->post('en_txtpageurl')),
                                    'ind_desc'=> mysql_real_escape_string($this->input->post('en_txtContent')),
                                    'ind_header_img'=> mysql_real_escape_string($upload_data['file_name'])
                                 );
                                $file_name = $upload_data['file_name'];
                            }
                        }
                        else
                        {
                            $update_data = array(
                                'ind_title'=> mysql_real_escape_string($this->input->post('txttitle')),
                                'ind_meta_description'=> mysql_real_escape_string($this->input->post('en_txtmetadescription')),
                                'ind_keywords'=> mysql_real_escape_string($this->input->post('en_txtkeywords')),
                                'ind_url_name'=> mysql_real_escape_string($this->input->post('en_txtpageurl')),
                                'ind_desc'=> mysql_real_escape_string($this->input->post('en_txtContent'))
                                
                            );
                        }
                        $result = $this->industriesmodel->action('update',$update_data,$edit_id);
                        if($result){
                            if(!empty($file_name)){
                                // delete old image
                                $old_image = $this->input->post('old_img');
                                $path = $old_image;
                                        $ext = pathinfo($path, PATHINFO_EXTENSION);
                                        $thumb_img = basename($path, ".".$ext); 
                                        $thumb_img = $thumb_img."_thumb.".$ext;
                                if(file_exists(SLIDER_HEADER_PATH . $old_image))        
                                {
                                    @unlink(SLIDER_HEADER_PATH . $old_image);
                                    @unlink(SLIDER_HEADER_PATH . $thumb_img);
                                }
                                
                                // end
                                $this->session->set_userdata('toast_message','Record updated successfully');
                                $this->session->set_userdata('uploaded_img',$file_name);
                                $this->session->set_userdata('redirect_to','industries');
                                redirect('header_crop');
                                //redirect('industries');
                            }
                            else
                            {
                                $this->session->set_userdata('toast_message','Record updated successfully');
                               // redirect('header_crop');
                                redirect('industries');
                            }
                                
                        }
                        else{
                            $this->session->set_userdata('toast_message','Unable to add record');
                        }
                    }
                    else{
                        $maxsequnce = $this->industriesmodel->getMaxSeq();
                        
                        $insert_data = array(
                            'ind_title'=> mysql_real_escape_string($this->input->post('txttitle')),
                            'ind_meta_description'=> mysql_real_escape_string($this->input->post('en_txtmetadescription')),
                            'ind_keywords'=> mysql_real_escape_string($this->input->post('en_txtkeywords')),
                            'ind_url_name'=> mysql_real_escape_string($this->input->post('en_txtpageurl')),
                            'ind_desc'=> mysql_real_escape_string($this->input->post('en_txtContent')),
                            'ind_status'=> 1,
                            'ind_sequence'=> $maxsequnce,
                            'is_deleted'=> 0
                        );
                        
                        
                        if ($_FILES['image']['name'] != '') 
                        {
                            $upload_data = $this->upload_industries_image();
                            if(array_key_exists('error', $upload_data))
                            {
                                $this->session->set_userdata('toast_error_message',$upload_data['error']);
                                redirect('industries/addedit','refresh');
                            }
                            else
                            {                        
                                $insert_data['ind_header_img'] = $upload_data['file_name'];
                                $file_name = $upload_data['file_name'];
                            }
                        }
                        
                        $result = $this->industriesmodel->action('insert',$insert_data);
                        if($result){
                            $this->session->set_userdata('toast_message','Record added successfully');
                            //$this->session->set_userdata('toast_message','Record updated successfully');
                            $this->session->set_userdata('uploaded_img',$file_name);
                            $this->session->set_userdata('redirect_to','industries');
                            redirect('header_crop');
                            //redirect('industries');
                            //redirect('industries');
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
        * Purpose: Method to upload industries image to the server.
        * Date: 12 Oct 2014
        * Input Parameter: None
        *  Output Parameter: 
        *  TRUE : if image upload succeeds.
        *  FALSE : if image upload fails.
        */
	public function upload_industries_image()
	{
            if (!file_exists(SLIDER_HEADER_PATH)) {
                mkdir(SLIDER_HEADER_PATH, 0700,true);
            }
            $file_name= $this->stripJunk($_FILES['image']['name']);//preg_replace('/[^a-zA-Z0-9_.]/s', '', $_FILES['image']['name']);
            
		$config =  array(
                  'upload_path'     => SLIDER_HEADER_PATH,
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
        
        // delete industries image 
        public function delete_industries($ind_id = 0)
        {
            $update_array = array(
                'is_deleted'=>1
            );
            $this->industriesmodel->action('update',$update_array,$ind_id);
            $this->session->set_userdata('toast_message','Record deleted successfully');
            redirect('industries');
        }
        
        public function update_status()
        {
            $sli_id = $this->input->post('ind_id');
            $this->industriesmodel->update_status($sli_id);
        }
        
        public function change_sequence($move='up',$mnu_id = 0)
        {
            $this->industriesmodel->change_sequence($mnu_id,$move);
            redirect('industries');
        }
        
         public function delete_header_image($id)
         {
             $result = $this->industriesmodel->delete_heder_image($id);
             echo  $result;
         }  
}
?>