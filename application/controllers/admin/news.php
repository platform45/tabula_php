<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH.'controllers/admin/Access.php';

/* * ****************** PAGE DETAILS ******************* */
/* @Programmer  : PK
 * @Maintainer  : PK
 * @Created     : 31 Aug 2016
 * @Modified    : 
 * @Description : This is gallery controller which is used
 * to show all gallery also add/edit/delete gallery.
 * ****************************************************** */

class News extends Access {
    /*
     * Purpose: Constructor.
     * Date: 31 Aug 2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct() {
        parent::__construct();

        $this->load->model('admin/newsmodel', 'newsmodel', TRUE);
        $this->load->model( 'webservices/notificationmodel', 'notificationmodel', TRUE );
    }

    //INDEX FUNCTION LOAD NEWS MANAGEMENT VIEW       
    public function index() {
       
            $data['sliderData'] = $this->newsmodel->getData();
            $this->template->view('news', $data);
        
    }

    //FUNCTION WHICH COUNT THE NUMBER OF NEWS IN DATABASE
    public function news_count() {
        $cnt = $this->newsmodel->news_count();

        echo $cnt;
    }

    //FUNCTION COUNT STATUS STATE OF NEWS    
    public function news_state_count() {
        $cnt = $this->newsmodel->news_state_count();
        echo $cnt;
    }

    //COMMEN FUNCTION FOR ADD EDIT NEWS
    public function addedit($edit_id = 0) {
       
            $data = array();
            $data['edit_id'] = $edit_id;
            $formData = array(
                'txttitle' => '',
                'txtshortdescription' => '',
                'txtsubtitle' => '',
                'txturl' => '',
                'txtdate' => '',
                'image' => ''
            );

            if (empty($_POST)) {

                if ($edit_id) {
                    $editData = $this->newsmodel->getData($edit_id);
                    if ($editData) {
                        $formData = array(
                            'txttitle' => $editData->news_title,
                            'txtshortdescription' => $editData->short_description,
                            'txtsubtitle' => $editData->news_desc,
                            'txturl' => $editData->news_link,
                            'txtdate' => $editData->news_date,
                            'image' => $editData->news_image,
                            'news_description_link' => 	$editData->news_description_link
                        );
                    }
                }
                $data['formData'] = $formData;
                $this->template->view('addnews', $data);
            } else {
                // process posted data
                $news_description_url = preg_replace('/[^A-Za-z0-9\-]/', '-', $this->input->post('txttitle'));
                $news_description_url = preg_replace('/-+/', '-', $news_description_url);
                $news_description_url = strtolower($news_description_url);

                $edit_id = $this->input->post('edit_id');
                if ($edit_id) {
                    $data = $this->input->post('txtdate');
                    $data1 = date("Y-m-d", strtotime($data));
                   //echo $data1;die;
                    $file_name = "";
                    if ($_FILES['image']['name'] != '') {
                        $upload_data = $this->upload_news_image();
                        if (array_key_exists('error', $upload_data)) {
                            $this->session->set_userdata('toast_error_message', $upload_data['error']);
                            redirect('news/addedit', 'refresh');
                        } else {
                            $update_data = array(
                                'news_title' => $this->input->post('txttitle'),
                                'short_description' => $this->input->post('txtshortdescription'),
                                'news_desc' => $this->input->post('txtsubtitle'),
                                'news_link' => $this->input->post('txturl'),
                                'news_description_link' => $news_description_url,
                                'news_date' => $data1,
                                'news_image' => $upload_data['file_name']
                            );
                            $file_name = $upload_data['file_name'];
                        }
                    } else {
                        $update_data = array(
                            'news_title' => $this->input->post('txttitle'),
                            'short_description' => $this->input->post('txtshortdescription'),
                            'news_desc' => $this->input->post('txtsubtitle'),
                            'news_link' => $this->input->post('txturl'),
                            'news_description_link' => $news_description_url,
                            'news_date' => $data1
                        );
                         //print_r($update_data);die;
                    }

                    $result = $this->newsmodel->action('update', $update_data, $edit_id);
                    if ($result) {
                        if (!empty($file_name)) {
                            // delete old image
                            $old_image = $this->input->post('old_img');
                            $path = $old_image;
                            $ext = pathinfo($path, PATHINFO_EXTENSION);
                            $thumb_img = basename($path, "." . $ext);
                            $thumb_img = $thumb_img . "_thumb." . $ext;
                            if (file_exists(NEWS_IMAGE_PATH . $old_image)) {
                                @unlink(NEWS_IMAGE_PATH . $old_image);
                                @unlink(NEWS_IMAGE_PATH . $thumb_img);
                            }

                            // end
                            $this->session->set_userdata('toast_message', 'Record updated successfully');
                            $this->session->set_userdata('uploaded_img', $file_name);
                            $this->session->set_userdata('redirect_to', 'admin/news');
                            redirect('admin/news_crop');
                        } else {
                            $this->session->set_userdata('toast_message', 'Record updated successfully');
                            redirect('admin/news');
                        }
                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record');
                    }
                } else {
                    $maxsequnce = $this->newsmodel->getMaxSeq();
                    $data = $this->input->post('txtdate');
                    $data1 = date("Y-m-d", strtotime($data));
                    $sTime = date("Y-m-d H:i:s");
                    $insert_data = array(
                        'news_title' => $this->input->post('txttitle'),
                        'short_description' => $this->input->post('txtshortdescription'),
                        'news_desc' => $this->input->post('txtsubtitle'),
                        'news_link' => $this->input->post('txturl'),
                        'news_description_link' => $news_description_url,
                        'news_date' => $data1,
                        'news_sequence' => $maxsequnce,
                        'news_status' => 1,
                        'is_deleted' => 0,
                        'news_created_on' => $sTime,
                        'news_modified_on' => $sTime
                    );
                    $type = "NEWS";
                    $text_message = 'New news "' . $this->input->post('txttitle') . '" has been added' ;

                    if ($_FILES['image']['name'] != '') {
                        $upload_data = $this->upload_news_image();

                        if (array_key_exists('error', $upload_data)) {
                            $this->session->set_userdata('toast_error_message', $upload_data['error']);
                            redirect('admin/news/addedit', 'refresh');
                        } else {
                            $insert_data['news_image'] = $upload_data['file_name'];
                            $file_name = $upload_data['file_name'];
                        }
                        $result = $this->newsmodel->action('insert', $insert_data);
                        $this->notificationmodel->news_promotion_push_notification($type,$text_message);

                        $this->session->set_userdata('toast_message', 'Record added successfully.');
                        $this->session->set_userdata('uploaded_img', $file_name);
                        $this->session->set_userdata('redirect_to', 'admin/news');
                        redirect('admin/news_crop');
                    } else {
                        $result = $this->newsmodel->action('insert', $insert_data);
                        $this->notificationmodel->news_promotion_push_notification($type,$text_message);

                        $this->session->set_userdata('toast_message', 'Record added successfully');
                        $this->session->set_userdata('redirect_to', 'admin/news');
                        redirect('admin/news');
                    }
                }
            }
         
        
    }

    /*
     * Purpose: Method to upload slider image to the server.
     * Input Parameter: None
     *  Output Parameter: 
     *  TRUE : if image upload succeeds.
     *  FALSE : if image upload fails.
     */

    //FUNCTION FOR UPLOAD IMAGE
    public function upload_news_image() {
        if (!file_exists(NEWS_IMAGE_PATH)) {
            mkdir(NEWS_IMAGE_PATH, 0700, true);
        }
        $file_name = stripJunk($_FILES['image']['name']);

        $config = array(
            'upload_path' => NEWS_IMAGE_PATH,
            'allowed_types' => "jpg|jpeg|JPG|JPEG",
            'overwrite' => FALSE,
            'max_size' => MAX_UPOAD_IMAGE_SIZE,
            'max_height' => "2160",
            'max_width' => "4096",
            'file_name' => $file_name
        );

        $this->load->library('upload', $config);
        if ($this->upload->do_upload('image')) {
            $upload_data = $this->upload->data();
            $data = array('file_name' => $upload_data['file_name']);
            return $data;
        } else {
            $error = array('error' => $this->upload->display_errors());
            return $error;
        }
    }

    // FUNCTION FOR DELETE NEWS
    public function delete_news($news_id = 0) {
        $update_array = array(
            'is_deleted' => 1
        );
        $this->newsmodel->action('update', $update_array, $news_id);
        $this->session->set_userdata('toast_message', 'Record deleted successfully');
        redirect('admin/news');
    }

    //FUNCTION FOR CHANGE STATUS OF NEWS
    public function update_news_status() {
        $news_id = $this->input->post('news_id');
        $this->newsmodel->update_status($news_id);
    }

    public function change_sequence($move = 'up', $mnu_id = 0) {
        $this->newsmodel->change_sequence($mnu_id, $move);
        redirect('admin/news');
    }

    public function check_duplicate_title($edit_id = 0)
    {
        $title = $this->input->post('txttitle');
        $duplicate_title = $this->newsmodel->check_for_duplicate_title($title,$edit_id);
        if($duplicate_title)
        {
            echo json_encode(FALSE);
        }
        else
        {
            echo json_encode(TRUE);
        }

    }

}

?>