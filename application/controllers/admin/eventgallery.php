<?php
/*
* Programmer Name:SK
* Purpose:Role Controller
* Date: 2 june 2015
* Dependency: slidermodel.php
*/
class Eventgallery extends CI_Controller
{
        /*
        * Purpose: Constructor.
        * Date: 2 june 2015
        * Input Parameter: None
        *  Output Parameter: None
        */
	function __construct()
        {
            parent::__construct();

            $this->load->model('admin/eventgallerymodel','ev_model',TRUE);
             $this->load->model('admin/eventmodel','eventmodel',TRUE);
            
        }
           
        /*
        * Purpose: To Load role
        * Date: 2 june 2015
        * Input Parameter: None
        * Output Parameter: None
        */
	public function index($event_id)
	{
           
             //$this->template->view('portfoliogallery');
            if($this->session->userdata('user_id'))
            {
               
                $data['event_id'] = $event_id;
                $result =$this->eventmodel->getData($event_id);
                $data['pm_title'] = $result->ne_title;
                $data['galleryData'] = $this->ev_model->getData($event_id);
                 
               
                $this->template->view('eventgallery',$data);
                
                
                
               
            }
            else
            {
                redirect('admin', 'refresh');
            }
	}
        
        public function add($event_id = 0)
	{
            if($this->session->userdata('user_id'))
            {
                
               if(empty($_POST))
                {
                       
                        $insert_data = array(
                            'event_id'=>$event_id,
                        );
                        if ($_FILES['image']['name'] != '') 
                        {
                            $upload_data = $this->upload_slider_image();
                            if(array_key_exists('error', $upload_data))
                            {
                                $this->session->set_userdata('toast_error_message',$upload_data['error']);
                                redirect('admin/eventgallery/index/'.$pm_id,'refresh');
                            }
                            else
                            {                        
                                $insert_data['image'] = $upload_data['file_name'];
                                $file_name = $upload_data['file_name'];
                            }
                        }
                        
                        $result = $this->ev_model->action('insert',$insert_data);
                        if($result){
                            $imagedata = getimagesize(EVENT_GALLERY_PATH .$upload_data['file_name']);
                            $width = $imagedata[0];
                            $height = $imagedata[1];
//                             if($width < 470 && $height < 366)
//                                { 
//                                    $thumb_ext = pathinfo($upload_data['file_name'], PATHINFO_EXTENSION);
//                                    $thumb = basename($upload_data['file_name'], ".".$thumb_ext); 
//                                    $thumb = $thumb.'_thumb.'.$thumb_ext;
//                                    $this->load->library('image_lib');
//                                    $configSize2['image_library']   = 'gd2';
//                                    $configSize2['source_image']    = SLIDER_IMAGE_PATH.$upload_data['file_name'];
//                                    $configSize2['maintain_ratio']  = TRUE;
//                                    $configSize2['width']           = $width;//100;
//                                    $configSize2['height']          = $height;//29;
//                                    $configSize2['new_image']       = $thumb;
//
//                                    $this->image_lib->initialize($configSize2);
//                                    $this->image_lib->resize();
//                                    $this->session->set_userdata('toasttime','Image limits are small then accepted for Croping.');
//                                    redirect('admin/eventgallery/index/'.$event_id);
//                                }
                            $this->session->set_userdata('toast_message','Record added successfully');
                            $this->session->set_userdata('uploaded_img',$file_name);
                            $this->session->set_userdata('redirect_to','admin/eventgallery/index/'.$event_id);
                            redirect('admin/eventgallery_crop');
                        }
                        else{
                            $this->session->set_userdata('toast_message','Unable to add record');
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
        * Purpose: Method to upload slider image to the server.
        * Date: 2 june 2015
        * Input Parameter: None
        *  Output Parameter: 
        *  TRUE : if image upload succeeds.
        *  FALSE : if image upload fails.
        */
	public function upload_slider_image()
	{
            if (!file_exists(EVENT_GALLERY_PATH)) {
                mkdir(EVENT_GALLERY_PATH, 0700,true);
            }
            $file_name= $this->stripJunk($_FILES['image']['name']);//preg_replace('/[^a-zA-Z0-9_.]/s', '', $_FILES['image']['name']);
            
		$config =  array(
                  'upload_path'     => EVENT_GALLERY_PATH,
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
        
        // delete slider image 
        public function delete_image()
        {
            $event_id = $this->input->post('event_id');
            $update_array = array(
                'is_delete'=>1
            );
            $this->ev_model->action('update',$update_array,$event_id);
            $this->session->set_userdata('toast_message','Record deleted successfully');
            //redirect('gallery');
        }
         public function change_sequence($move='up',$pg_id = 0,$pm_id)
        {
            $result=$this->ev_model->change_sequence($pg_id,$move,$pm_id);
            redirect('portfoliogallery/index/'.$pm_id);
        }
}
?>