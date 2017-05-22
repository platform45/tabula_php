<?php
/******************** PAGE DETAILS ********************/
/* @Programmer  : SK.
 * @Maintainer  : SK.
 * @Created     : 27 Nov 2014
 * @Modified    : 
 * @Description : This is controller for cropping.
********************************************************/

class Prod_crop extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
    }
    
    public function index()
    {
            $data['sImagePath'] = $this->config->item('assets')."upload/products/";
            $imageName = $this->session->userdata('uploaded_img');
            $data['sImageName'] = $imageName;
            # PROCESSING ------------------------------------------------------------------
            $this->template->view('prod_crop',$data);
    }
    
    public function process($red_to = ""){
        if($red_to == "closed"){
            
            $redirect_to = $this->session->userdata('redirect_to');
            $imageName = $this->session->userdata('uploaded_img');
            $image_path =  "assets/upload/products/".$imageName;
            $upload_data = array(
                                    'file_name'=> $imageName,
                                    'full_path'=>$image_path
                                );
            $this->resize_uploaded_if_cancel($upload_data);
            redirect($redirect_to);
        }
        else{
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') 
			{
                if($_POST['dataW'] > 0 && $_POST['dataH'] > 0) 
                {
                        //------------------------CROP LARGE SIZE IMAGE---------------------------------------------//
                        //echo APPPATH;die;
                        $image_path =  "assets/upload/products/".$_POST['imageName'];

                        $targ_w = 277;
                        $targ_h = 255;
                        $jpeg_quality = 90;
                        $img_r = imagecreatefromjpeg($image_path);

                        $dst_r = ImageCreateTrueColor($targ_w, $targ_h);
                        $x =  $_POST['dataX'];
                        $y = $_POST['dataY'];
                        $w = $_POST['dataW'];
                        $h = $_POST['dataH'];

                        //$ext = pathinfo($dst_r, PATHINFO_EXTENSION);

                        imagecopyresampled($dst_r, $img_r, 0, 0, $x, $y , $targ_w, $targ_h, $w, $h );
                        $image_path = FP_IMAGE_PATH .$_POST['imageName'];

                        imagejpeg($dst_r, $image_path,$jpeg_quality);
                        // Remove from memory
                        imagedestroy($dst_r);

                        //RESIZE IMAGE TO GET THUMBNAIL, LARGE SIZE IMAGES
                        $upload_data = array(
                                'file_name'=>  $_POST['imageName'],
                                'full_path'=>$image_path
                        );
                        $this->upload_products_image($upload_data);
                        $redirect_to = $this->session->userdata('redirect_to');
                        redirect($redirect_to);
                }
            }
        }
    }
    
    public function upload_products_image($upload_data = array())
	{
            
		$config_resize['image_library'] = 'gd2';
                $config_resize['source_image'] = $upload_data['full_path'];
                $config_resize['width'] = 178;
                $config_resize['height'] = 164;
                $config_resize['maintain_ratio'] = TRUE;
                $config_resize['create_thumb'] = TRUE;

                $this->load->library('image_lib',$config_resize); 
                $this->image_lib->resize();
	}
        
   public function resize_uploaded_if_cancel($upload_data = array())
	{
            
            $this->load->library('image_lib');

                /* Second size */    
              
                $configSize2['image_library']   = 'gd2';
                $configSize2['source_image']    = FP_IMAGE_PATH.$upload_data['file_name'];
                $configSize2['create_thumb']    = TRUE;
                $configSize2['maintain_ratio']  = TRUE;
                $configSize2['width']           = 100;
                $configSize2['height']          = 29;
                $configSize2['new_image']   = $upload_data['file_name'];

                $this->image_lib->initialize($configSize2);
                $this->image_lib->resize();
                $this->image_lib->clear();
                
                 /* First size */
                $configSize1['image_library']   = 'gd2';
                $configSize1['source_image']    = FP_IMAGE_PATH.$upload_data['file_name'];
                $configSize1['create_thumb']    = FALSE;
                $configSize1['maintain_ratio']  = TRUE;
                $configSize1['width']           = 758;
                $configSize1['height']          = 218;
                $configSize1['new_image']   = $upload_data['file_name'];

                $this->image_lib->initialize($configSize1);
                $this->image_lib->resize();
                $this->image_lib->clear();
                

	}     
        
   
}