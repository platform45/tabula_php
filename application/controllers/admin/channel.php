<?php
/*
* Programmer Name:SK
* Purpose:Role Controller
* Date:19 Dec 2014
* Dependency: channelmodel.php
*/
class Channel extends CI_Controller
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

            $this->load->model('admin/channelmodel','',TRUE);
            
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
                $data['channelData'] = $this->channelmodel->getData();                
                $this->template->view('channel',$data);
            }
            else
            {
                redirect('admin', 'refresh');
            }
	}
        
        public function freeVideo($channel_id)
	{
            if($this->session->userdata('user_id'))
            {
              
                $data['videoData'] = $this->channelmodel->getVideos($channel_id);
                $data['freeVideosData'] = $this->channelmodel->getFreeVideos($channel_id);
                $videos =array();
                $cnt =0;
                if(@$data['freeVideosData'])
                {
                foreach($data['freeVideosData'] as $video)
                {
                    $videos[$cnt] = $video["vid_id"];
                    $cnt++;
                }
            }
                //print_r($videos);die;
                $data['allVideoData'] = $videos;
                $data['categoriesData'] = $this->channelmodel->getCategories($channel_id);
                $data['channel_id'] = $channel_id;
                $this->template->view('freesequencialvideo',$data);
            }
           
	}
        
        public function sequentialVideo()
	{
            if($this->session->userdata('user_id'))
            {
              $vids =array();
              if($_POST)
              {                    
                $vids = $this->input->post("selectto");
                $channel_id = $this->input->post("channel_id");
                $result =$this->channelmodel->addsequenciaVideo($vids,$channel_id);
                $this->session->set_userdata('toast_message','Free videos added succesfully');
                redirect('admin/channel'); 
              }
            }
           
	}
         public function getCategories($channel_id)
	{
                //$data['videoData'] = $this->channelmodel->getVideos($channel_id);
                $data['categoriesData'] = $this->channelmodel->getCategories($channel_id);
                
                $this->template->view('freesequencialvideo',$data);
	}
        public function channel_count()
        {
            $cnt=$this->channelmodel->channel_count();
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
                    'txtoneliner'=>'',
                    'image'=>'',
                    'channel_icon'=>''
                );
                
                if(empty($_POST))
                {
                    if($edit_id)
                    {
                        $editData = $this->channelmodel->getData($edit_id);
                        //print_r($editData);die;
                        if($editData){
                            $formData = array(
                                'txttitle'=>$editData->channel_title,
                                'txtoneliner'=>$editData->channel_oneliner,
                                'image'=>$editData->channel_image,
                                'channel_icon'=>$editData->channel_icon
                            );
                        }
                    }
                    $data['formData']=$formData;    
                    $this->template->view('addchannel',$data);
                }
                else{
                    // process posted data
                    $edit_id = $this->input->post('edit_id');
                    if($edit_id){
                        $file_name = "";
                        if ($_FILES['image']['name'] != '') 
                        {
                            $upload_data = $this->upload_brand_image();
                            if(array_key_exists('error', $upload_data))
                            {
                                $this->session->set_userdata('toast_error_message',$upload_data['error']);
                                redirect('admin/channel/addedit','refresh');
                            }
                            else
                            {                        
                                $update_data = array(
                                    'channel_title'=> mysql_real_escape_string($this->input->post('txttitle')),
                                    'channel_oneliner'=> $this->input->post('txtoneliner'),
                                    'channel_image'=> mysql_real_escape_string($upload_data['file_name']),                                   
                                
                                    );
                                $file_name = $upload_data['file_name'];
                            }
                        }
                        else
                        {
                          
                          $update_data = array(
                                'channel_title'=> mysql_real_escape_string($this->input->post('txttitle')),
                                'channel_oneliner'=> $this->input->post('txtoneliner'),
                                
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
                                    $update_data['channel_icon']= $upload_data['file_name']; 
                            }
                        }
                            
                           
                        }
                        
                        $result = $this->channelmodel->action('update',$update_data,$edit_id);
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
                                $this->session->set_userdata('redirect_to','admin/channel');
                                redirect('admin/channel_crop');
                            }
                            else
                            {
                                $this->session->set_userdata('toast_message','Record updated successfully');
                                redirect('admin/channel');
                            }
                                
                        }
                        else{
                            $this->session->set_userdata('toast_message','Unable to add record');
                        }
                    }
                    else{
                        $maxsequnce = $this->channelmodel->getMaxSeq();
                        
                        $insert_data = array(
                            'channel_title'=> mysql_real_escape_string($this->input->post('txttitle')),
                            'channel_oneliner'=> $this->input->post('txtoneliner'),
                            'channel_status'=> 1,
                            'channel_sequence'=> $maxsequnce,
                            'is_deleted'=> 0,
                            'created_on'=> date("Y-m-d h:i:s")
                        );
                        if ($_FILES['image']['name'] != '') 
                        {
                            $upload_data = $this->upload_brand_image();
                            if(array_key_exists('error', $upload_data))
                            {
                                $this->session->set_userdata('toast_error_message',$upload_data['error']);
                                redirect('admin/channel/addedit','refresh');
                            }
                            else
                            {                        
                                $insert_data['channel_image'] = $upload_data['file_name'];
                                $file_name = $upload_data['file_name'];
                            }
                        }
                         $icon = "";
                        if ($_FILES['icon']['name'] != '') 
                        {
                            //echo $_FILES['icon']['name'];die;
                            $upload_data = $this->upload_icon_image();
                            if(array_key_exists('error', $upload_data))
                            {
                                $this->session->set_userdata('toast_error_message',$upload_data['error']);
                                redirect('admin/channel/addedit','refresh');
                            }
                            else
                            {    
                                    $insert_data['channel_icon']= $upload_data['file_name']; 
                            }
                        }
                        $result = $this->channelmodel->action('insert',$insert_data);
                        if($result){
                            $this->session->set_userdata('toast_message','Record added successfully');
                            //$this->session->set_userdata('toast_message','Record updated successfully');
                            $this->session->set_userdata('uploaded_img',$file_name);
                            $this->session->set_userdata('redirect_to','admin/channel');
                            redirect('admin/channel_crop');
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
	public function upload_brand_image()
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
        
        
        public function upload_icon_image()
	{
            if (!file_exists(CHANNEL_ICON_PATH)) {
                mkdir(CHANNEL_ICON_PATH, 0700,true);
            }
            $file_name= $this->stripJunk($_FILES['icon']['name']);//preg_replace('/[^a-zA-Z0-9_.]/s', '', $_FILES['image']['name']);
            
         
		$config1 =  array(
                  'upload_path'     => CHANNEL_ICON_PATH,
                  'allowed_types'   => "jpg|png|jpeg",
                  'overwrite'       => FALSE,
                  'max_size'        => MAX_UPOAD_IMAGE_SIZE,
                  'max_height'      => "2160",
                  'max_width'       => "4096",
                  'file_name'        => $file_name
                );
              
                $this->load->library('upload');
                $this->upload->initialize($config1);
                $upload_data=array();
                if($this->upload->do_upload('icon'))
                {
                    //echo "hello";die;
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
        public function delete_channel($brand_id = 0)
        {
            $update_array = array(
                'is_deleted'=>1
            );
            $this->channelmodel->action('update',$update_array,$brand_id);
            $this->session->set_userdata('toast_message','Record deleted successfully');
            redirect('admin/channel');
        }
        
        public function update_channel_status()
        {
            $channel_id = $this->input->post('channel_id');
            $this->channelmodel->update_channel_status($channel_id);
        }
        
        public function change_sequence($move='up',$mnu_id = 0)
        {
            $this->channelmodel->change_sequence($mnu_id,$move);
            redirect('admin/channel');
        }
        
        public function check_title_exists($id = FALSE)
        {
           
           if($id === FALSE)
                {
                       
                        $title = $this->input->post('title');
                        if($title!='')
                        {
                                $url_exists = $this->channelmodel->check_title_exists($title);
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
                                $url_exists = $this->channelmodel->check_title_exists($title,$id);
                                if($url_exists && (strcmp($url_exists,$title)!=0))
                                        echo json_encode(TRUE);
                                else
                                        echo json_encode(FALSE);
                        }
                        else
                                echo json_encode(TRUE);
                }
        }
         
        public function check_having_category(){
            
            $channel_id =$this->input->post("channel_id");
            $this->db->select("cat_id");
            $this->db->where("is_delete",0);
            $this->db->where("channel_id",$channel_id);
            $result =$this->db->get("category");
            $res1 = $result->num_rows(); 
            $this->db->select("ad_id");
            $this->db->where("is_deleted",0);
            $this->db->where("channel_id",$channel_id);
            $query =$this->db->get("ads");
            $res2 = $query->num_rows(); 
            if($res1 > 0 || $res2 > 0)
            {
                echo 0;
            }
            else
            {
                echo 1;
            }
                
        }
        
         public function getVideo(){
            
            $cat_id =$this->input->post("cat_id");
            $channel_id =$this->input->post("channel_id");
            $data['freeVideosData'] = $this->channelmodel->getFreeVideos($channel_id);
            $result = $this->channelmodel->getVideos($channel_id,$cat_id);
            $option="";
            if($result)
            {
                if(@$data['freeVideosData'])
                {
                    foreach($data['freeVideosData'] as $video)
                    {
                        $videos[$cnt] = $video["vid_id"];
                        $cnt++;
                    }
                }
                foreach($result as $vid)
                {
                if(!in_array($vid['vid_id'],$videos))
                 {
                    $option .=  '<option value="'.$vid['vid_id'].'">'. $vid['video_title'].'</option>';
                 }
                }
            }
               echo  $option;
        }

}
?>