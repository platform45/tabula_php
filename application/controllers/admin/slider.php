<?php

/*
 * Programmer Name: Akash Deshmukh
 * Purpose: Slider Controller
 * Date: 02 Sept 2016
 * Dependency: slidermodel.php
 */

class Slider extends CI_Controller {
    /*
     * Purpose: Constructor.
     * Date: 02 Sept 2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct() {
        parent::__construct();

        $this->load->model('admin/slidermodel', 'slidermodel', TRUE);
    }

    //To load model
    public function index() {
        if ($this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_ADMIN_TYPE || $this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_SUBADMIN_TYPE) {
            $data['sliderData'] = $this->slidermodel->getData();
            $this->template->view('slider', $data);
        } else {
            redirect('admin', 'refresh');
        }
    }

    //Function of slider count
    public function slider_count() {
        $cnt = $this->slidermodel->slider_count();
        echo $cnt;
    }

    //Function of slider state count
    public function slider_state_count() {
        $cnt = $this->slidermodel->slider_state_count();
        echo $cnt;
    }

    public function addedit($edit_id = 0) {
        if ($this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_ADMIN_TYPE || $this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_SUBADMIN_TYPE) {
            $data = array();
            $data['edit_id'] = $edit_id;
            $formData = array(
                'txttitle' => '',
                'txturl' => '',
                'image' => ''
            );

            if (empty($_POST)) {
                if ($edit_id) {
                    $editData = $this->slidermodel->getData($edit_id);
                    if ($editData) {
                        $formData = array(
                            'txttitle' => $editData->sli_title,
                            'image' => $editData->sli_image
                        );
                    }
                }
                $data['formData'] = $formData;
                $this->template->view('addslider', $data);
            } else {
                // process posted data
                $edit_id = $this->input->post('edit_id');
                if ($edit_id) {
                    $file_name = "";
                    if ($_FILES['image']['name'] != '') {
                        $upload_data = $this->upload_slider_image();
                        if (array_key_exists('error', $upload_data)) {
                            $this->session->set_userdata('toast_error_message', $upload_data['error']);
                            redirect('slider/addedit', 'refresh');
                        } else {
                            $update_data = array(
                                'sli_title' => $this->input->post('txttitle'),
                                'sli_image' => $upload_data['file_name']
                            );
                            $file_name = $upload_data['file_name'];
                        }
                    } else {
                        $update_data = array(
                            'sli_title' => $this->input->post('txttitle')
                        );
                    }

                    $result = $this->slidermodel->action('update', $update_data, $edit_id);
                    if ($result) {
                        if (!empty($file_name)) {
                            // delete old image
                            $old_image = $this->input->post('old_img');
                            $path = $old_image;
                            $ext = pathinfo($path, PATHINFO_EXTENSION);
                            $thumb_img = basename($path, "." . $ext);
                            $thumb_img = $thumb_img . "_thumb." . $ext;
                            if (file_exists(SLIDER_IMAGE_PATH . $old_image)) {
                                @unlink(SLIDER_IMAGE_PATH . $old_image);
                                @unlink(SLIDER_IMAGE_PATH . $thumb_img);
                            }

                            // end
                            $this->session->set_userdata('toast_message', 'Record updated successfully.');
                            $this->session->set_userdata('uploaded_img', $file_name);
                            $this->session->set_userdata('redirect_to', 'admin/slider');
                            redirect('admin/slider_crop');
                        } else {
                            $this->session->set_userdata('toast_message', 'Record updated successfully.');
                            redirect('admin/slider');
                        }
                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record.');
                    }
                } else {
                    $maxsequnce = $this->slidermodel->getMaxSeq();
                    $sTime = date("Y-m-d H:i:s");
                    $insert_data = array(
                        'sli_title' => $this->input->post('txttitle'),
                        'sli_status' => '1',
                        'sli_sequence' => $maxsequnce,
                        'sli_type' => "Slider",
                        'is_deleted' => '0',
                        'sli_created_on' => $sTime,
                        'sli_modified_on' => $sTime
                    );
                    if ($_FILES['image']['name'] != '') {
                        $upload_data = $this->upload_slider_image();

                        if (array_key_exists('error', $upload_data)) {
                            $this->session->set_userdata('toast_error_message', $upload_data['error']);
                            redirect('admin/slider/addedit', 'refresh');
                        } else {
                            $insert_data['sli_image'] = $upload_data['file_name'];
                            $file_name = $upload_data['file_name'];
                        }
                    }

                    $result = $this->slidermodel->action('insert', $insert_data);
                    if ($result) {
                        $this->session->set_userdata('toast_message', 'Record added successfully.');
                        $this->session->set_userdata('uploaded_img', $file_name);
                        $this->session->set_userdata('redirect_to', 'admin/slider');
                        redirect('admin/slider_crop');
                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record.');
                    }
                }
            }
        } else {
            redirect('admin', 'refresh');
        }
    }

    //Method for image upload
    function stripJunk($string) {
        $string = str_replace(" ", "-", trim($string));
        $string = preg_replace("/[^a-zA-Z0-9-.]/", "", $string);
        $string = strtolower($string);
        return $string;
    }

    //Function for uploading slider image
    public function upload_slider_image() {
        if (!file_exists(SLIDER_IMAGE_PATH)) {
            mkdir(SLIDER_IMAGE_PATH, 0700, true);
        }
        $file_name = $this->stripJunk($_FILES['image']['name']);
        $config = array(
            'upload_path' => SLIDER_IMAGE_PATH,
            'allowed_types' => "jpg|png|jpeg|JPG|JPEG|PNG",
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

    // delete slider image 
    public function delete_slider($slider_id = 0) {
        $update_array = array(
            'is_deleted' => '1'
        );
        $this->slidermodel->action('update', $update_array, $slider_id);
        $this->session->set_userdata('toast_message', 'Record deleted successfully.');
        redirect('admin/slider');
    }

    public function update_slider_status() {
        $sli_id = $this->input->post('sli_id');
        $this->slidermodel->update_status($sli_id);
    }

    public function change_sequence($move = 'up', $mnu_id = 0) {
        $this->slidermodel->change_sequence($mnu_id, $move);
        redirect('admin/slider');
    }

}

?>