<?php
/*
* Programmer Name:SK
* Purpose:Role Controller
* Date:19 Dec 2014
* Dependency: videomodel.php
*/
class Video extends CI_Controller
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

            $this->load->model('admin/videomodel','videomodel',TRUE);
            ini_set('upload_max_filesize', '200M');
            ini_set('post_max_size', '200M');                               
            ini_set('max_input_time', 3000);                                
            ini_set('max_execution_time', 3000);
            
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
                $data['videoData'] = $this->videomodel->getData();
                $this->template->view('video',$data);
            }
            else
            {
                redirect('admin', 'refresh');
            }
	}
        
   
        public function video_count()
        {
            $cnt=$this->videomodel->video_count();
            echo $cnt;
        }
        
         
        public function addedit($edit_id = 0)
	{
         
            
            if($this->session->userdata('user_id'))
            {
                $data = array();
                $data['edit_id'] = $edit_id;
                $data['channelArray']=$this->videomodel->getChannels();
               
                $formData = array(
                    'vid_id'=>'',
                    'channel_id'=>'',
                    'category_id'=>'',
                    'channel_title'=>'',
                    'video_title'=>'',
                    'video_desc'=>'',
                    'video'=>'',
                    "video_type"=>""
                   
                );
                
                if(empty($_POST))
                {
                    if($edit_id)
                    {
                        $editData = $this->videomodel->getData($edit_id);
                       
                       
                        if($editData){
                            $formData = array(
                                'vid_id'=>$editData->vid_id,
                                'channel_id'=>$editData->channel_id,
                                'category_id'=>$editData->category_id,
                                'channel_title'=>$editData->channel_title,
                                'category_name'=>$editData->category_name,
                                'video_title'=>$editData->video_title,
                                'video_desc'=>$editData->video_desc,
                                'video_status'=>$editData->video_status,
                                'video'=>$editData->video,
                                'video_type'=>$editData->video_type,
                                
                            );
                             $data['categoryArray']=$this->videomodel->getCategories($editData->channel_id);
                            
                        }
                    }
                    $data['formData']=$formData;    
                    $this->template->view('addvideo',$data);
                }
                else{
                   
                    // process posted data
                    $edit_id = $this->input->post('edit_id');
                    if($edit_id){
                        $file_name = "";
                        if ($_FILES['video']['name'] != '') 
                        {
                            
                            
                            $upload_data = $this->upload_vedio();
                            if(array_key_exists('error', $upload_data))
                            {
                                
                                $this->session->set_userdata('toast_error_message',$upload_data['error']);
                                redirect('admin/video/addedit/'.$edit_id,'refresh');
                            }
                            else
                            {
                                
                                $update_data = array(
                                    
                                        'video_title'=> $this->input->post('video_title'),
                                        'video_desc'=> $this->input->post('video_desc'),
                                        'video_type'=> $this->input->post('video_type'),
                                        'video'=> $upload_data['file_name'],                                        
                                        'category_id'=> $this->input->post('category_id'),
                                        'channel_id' => $this->input->post('channel_id')
                                );
                                $file_name = $upload_data['file_name'];
                            }
                        }
                        else
                        {
                            
                            
                            $update_data = array(                                 
                            'video_title'=> $this->input->post('video_title'),
                            'video_desc'=> $this->input->post('video_desc'),
                            'video_type'=> $this->input->post('video_type'),                                               
                            'category_id'=> $this->input->post('category_id'),
                            'channel_id' => $this->input->post('channel_id')                                  
                                   
                                );
                        }
                       
                        $result = $this->videomodel->action('update',$update_data,$edit_id);
                        if($result){
                            
                              
                                $this->session->set_userdata('toast_message','Record updated successfully.');
                                redirect('admin/video');
                          
                                
                        }
                        else{
                            $this->session->set_userdata('toast_message','Unable to add record');
                        }
                    }
                    else{
                      
                        $maxsequnce = $this->videomodel->getMaxSeq();
                        
                        $insert_data = array(
                           
                           
                            'video_title'=> $this->input->post('video_title'),
                            'video_desc'=> $this->input->post('video_desc'),
                            'video_type'=> $this->input->post('video_type'),
                            'video'=> $this->input->post('video'),
                            'video_sequence'=> $maxsequnce,
                            'category_id'=> $this->input->post('category_id'),
                            'created_on' => date("Y-m-d h:i:s"),
                            'channel_id' => $this->input->post('channel_id')
                        );
                        
                        if ($_FILES['video']['name'] != '') 
                        {
                           
                            $upload_data = $this->upload_vedio();
                            if(array_key_exists('error', $upload_data))
                            {
                                
                                $this->session->set_userdata('toast_error_message',$upload_data['error']);
                                redirect('admin/video/addedit','refresh');
                            }
                            else
                            {                        
                                $insert_data['video'] = $upload_data['file_name'];
                                $file_name = $upload_data['file_name'];
                            }
                        }
                        
                        $result = $this->videomodel->action('insert',$insert_data);
                       
                        
                        
                        if($result){
                            // check is video exist if yes then send push for added video
                            //else send for added channel
                            $channel_id  =$this->input->post('channel_id');
                            $isFirst = $this->videomodel->checkVideo($channel_id);
                            $channelTitle = $this->videomodel->getChannelTitle($channel_id);
                            
                            if($isFirst==0)
                            {
                                $type = "channel";
                                $title =$channelTitle;
                                sendPush($channel_id,$title,$type);
                            }
                            else
                            {
                                $type = "video";
                                $title =$this->input->post('video_title');
                                sendPush($channel_id,$title,$type);
                            }
                            $this->session->set_userdata('toast_message','Record added successfully.');
                            //$this->session->set_userdata('toast_message','Record updated successfully');
                            $this->session->set_userdata('uploaded_img',$file_name);
                            $this->session->set_userdata('redirect_to','products');
                           
                            redirect('admin/video');
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
        * Purpose: Method to upload products image to the server.
        * Date: 12 Oct 2014
        * Input Parameter: None
        *  Output Parameter: 
        *  TRUE : if image upload succeeds.
        *  FALSE : if image upload fails.
        */
	public function upload_vedio()
	{
            if (!file_exists(VIDEO_PATH)) {
                mkdir(VIDEO_PATH, 0755,true);
            }
           
            $file_name= $this->stripJunk($_FILES['video']['name']);//preg_replace('/[^a-zA-Z0-9_.]/s', '', $_FILES['image']['name']);
            
          	 $config =  array(
                  'upload_path'     => VIDEO_PATH,
                  'overwrite'       => FALSE,
                  'allowed_types'  => "flv|mp4|avi|mpeg",
                  'max_size'        => MAX_UPOAD_VIDEO_SIZE,
                  'max_height'      => "2160",
                  'max_width'       => "4096",
                  'file_name'        => $file_name
                );
                $this->load->library('upload', $config);
                if($this->upload->do_upload('video'))
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
        
        // delete products image 
       
        public function update_video_status()
        {
           $prod_id = $this->input->post('vid_id');
           $this->videomodel->update_status($prod_id);
        }
        
         public function delete_video($video_id = 0)
        {
            $update_array = array(
                'is_deleted'=>1
            );
            $this->videomodel->action('update',$update_array,$video_id);
            $this->session->set_userdata('toast_message','Record deleted successfully.');
            redirect('admin/video');
        }
        
        public function update_productshome_status()
        {
            $prod_id = $this->input->post('prod_at_home');
            $this->videomodel->update_productshome_status($prod_id);
        }
        
        
        public function change_sequence($move='up',$mnu_id = 0)
        {
            $this->videomodel->change_sequence($mnu_id,$move);
            redirect('products/getFeatureData');
        }
        
        public function getcategories()
        {
            $channel_id = $this->input->post("channel_id");
            $categoryArray=$this->videomodel->getCategories($channel_id);
            $option= "<option value=''>Select category</option>";
            foreach($categoryArray as $result)
            {
                $option .=  "<option value='".$result['cat_id']."'>".$result['category_name']."</option>";
            }
            echo $option;die;
        }
        
          public function check_title_exists($id = FALSE)
        {
           $channel_id = $this->input->post('channel_id');
           $category_id = $this->input->post('category_id');
           if($id === FALSE)
                {
                       
                        $title = $this->input->post('title');
                        if($title!='')
                        {
                                $url_exists = $this->videomodel->check_title_exists($title,$channel_id,$category_id);
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
                                $url_exists = $this->videomodel->check_title_exists($title,$id,$channel_id,$category_id);
                                if($url_exists && (strcmp($url_exists,$title)!=0))
                                        echo json_encode(TRUE);
                                else
                                        echo json_encode(FALSE);
                        }
                        else
                                echo json_encode(TRUE);
                }
        }
}
?>