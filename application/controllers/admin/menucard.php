<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH.'controllers/admin/Access.php';

/*
 * Programmer Name: Akash Deshmukh
 * Purpose: Foodmenu Controller
 * Date: 02 Sept 2016
 * Dependency: foodmenumodel.php
 */

class Menucard extends Access {
    /*
     * Purpose: Constructor.
     * Date: 02 Sept 2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct() {
        parent::__construct();

        $this->load->model('admin/menucardmodel', 'menucardmodel', TRUE);
        $this->load->model('admin/restaurantmodel', 'restaurantmodel', TRUE);
    }

    //Function to load the grid view
    public function menucard_list() {
        $user_id = $this->session->userdata('user_id');
        $email = $this->session->userdata('user_email');
        $data['members'] = $this->restaurantmodel->getData($user_id);
        $data['user_id'] = $user_id;

        if ($this->session->userdata('user_id')) {

            $data['sliderData'] = $this->menucardmodel->getData($user_id);
            $this->template->view('menucard', $data);
        } else {
            redirect('admin', 'refresh');
        }
    }

    //Function for the foodmenu count
    public function slider_count() {

        $cnt = $this->menucardmodel->slider_count();
        echo $cnt;
    }

    //Function for loading the foodmenu state count
    public function slider_state_count() {

        $cnt = $this->menucardmodel->slider_state_count();
        echo $cnt;
    }

    //Function for add and edit operations
    public function addedit($user_id = 0, $edit_id = 0) {

        if ($this->session->userdata('user_id')) {
            $user_id = $this->session->userdata('user_id');
        $email = $this->session->userdata('user_email');
            $data = array();
            $data['edit_id'] = $edit_id;
            $data['user_id'] = $user_id;
            $formData = array(
                'image' => '',
                'user_id' => ''
            );

            if (empty($_POST)) {
                if ($edit_id) {
                    $editData = $this->menucardmodel->getData($edit_id);
                    if ($editData) {
                        $formData = array(
                            'image' => $editData->fm_image
                        );
                    }
                }

                $data['user_id']=$user_id;
                $data['formData'] = $formData;
                $this->template->view('addmenucard', $data);
            } else {
                // process posted data
                $edit_id = $this->input->post('edit_id');
                if ($edit_id) {
                    if ($result) {

                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record.');
                    }
                } else {
                    $maxsequnce = $this->menucardmodel->getMaxSeq();
                    $sTime = date("Y-m-d H:i:s");
                    $data['user_id'] = $user_id;
                    $insert_data = array(
                        'status' => '1',
                        'is_deleted' => '0',
                        'created_on' => $sTime,
                        'user_id' => $user_id,
                        'created_by' => $user_id,
                        'menu_image_seq' => $maxsequnce
                    );
                    if ($_FILES['image']['name'] != '') {
                        $upload_data = $this->upload_foodmenu_image();

                        if (array_key_exists('error', $upload_data)) {
                            $this->session->set_userdata('toast_error_message', $upload_data['error']);
                            redirect('admin/menucard/addedit', 'refresh');
                        } else {
                            $insert_data['fm_image'] = $upload_data['file_name'];
                            $file_name = $upload_data['file_name'];
                        }
                    }
                    $data['user_id']=$user_id;
                    $result = $this->menucardmodel->action('insert', $insert_data);

                    if ($result) {
                         $this->session->set_userdata('uploaded_img', $file_name);
                            redirect('admin/menucard/menucard_list');
                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record.');
                    }
                }
            }
        } else {
            redirect('admin', 'refresh');
        }
    }

    function stripJunk($string) {
        $string = str_replace(" ", "-", trim($string));
        $string = preg_replace("/[^a-zA-Z0-9-.]/", "", $string);
        $string = strtolower($string);
        return $string;
    }

    //Method to upload foodmenu image to the server.
    public function upload_foodmenu_image() {
        if (!file_exists(FOODMENU_IMAGE_PATH)) {
            mkdir(FOODMENU_IMAGE_PATH, 0700, true);
        }
        $file_name = $this->stripJunk($_FILES['image']['name']);
        $this->load->library('image_lib');
        $config = array(

            'image_library' => 'gd2',
            'upload_path' => FOODMENU_IMAGE_PATH,
            'allowed_types' => "jpg|jpeg|JPG|JPEG",
            'overwrite' => TRUE,
            'max_size' => MAX_UPOAD_IMAGE_SIZE,
            'create_thumb' =>TRUE,
            'maintain_ratio' =>TRUE,
            'height' => "1600",
            'width' => "800",
            'file_name' => $file_name
        );


        $this->load->library('upload', $config);
        if ($this->upload->do_upload('image')) {
            $upload_data = $this->upload->data();

            $this->resize_uploaded_if_cancel($upload_data);

            $data = array('file_name' => $upload_data['file_name']);
            return $data;
        } else {
            $error = array('error' => $this->upload->display_errors());
            return $error;
        }
    }


        public function resize_uploaded_if_cancel($upload_data = array())
    {

            $this->load->library('image_lib');

                /* Second size */

                $configSize2['image_library']   = 'gd2';
                $configSize2['source_image']    = FOODMENU_IMAGE_PATH.$upload_data['file_name'];
                $configSize2['create_thumb']    = TRUE;
                $configSize2['maintain_ratio']  = TRUE;
                $configSize2['width']           = 800;
                $configSize2['height']          = 1600;
                $configSize2['new_image']   = $upload_data['file_name'];

                $this->image_lib->initialize($configSize2);
                $this->image_lib->resize();
                $this->image_lib->clear();

                 /* First size */
                $configSize1['image_library']   = 'gd2';
                $configSize1['source_image']    = FOODMENU_IMAGE_PATH.$upload_data['file_name'];
                $configSize1['create_thumb']    = FALSE;
                $configSize1['maintain_ratio']  = TRUE;
                $configSize1['width']           = 800;
                $configSize1['height']          = 1600;
                $configSize1['new_image']   = $upload_data['file_name'];

                $this->image_lib->initialize($configSize1);
                $this->image_lib->resize();
                $this->image_lib->clear();


    }

    //Function to delete foodmenu image
    public function delete_slider( $edit_id = 0) {
        $update_array = array(
            'is_deleted' => '1'
        );
        $this->menucardmodel->action('update', $update_array, $edit_id);
        $this->session->set_userdata('toast_message', 'Record deleted successfully.');
        redirect('admin/menucard/menucard_list/');
    }

    //Update the foodmenu status
    public function update_slider_status() {
        $sli_id = $this->input->post('fm_id');
        $this->menucardmodel->update_status($sli_id);
    }

    //Function for changing the sequence of the lists

    public function change_sequence($move = 'up', $user_id = 0, $mnu_id = 0) {
        $this->menucardmodel->change_sequence($mnu_id, $move);
        redirect('admin/menucard/menucard_list/');
    }

}

?>