<?php

/*
 * Programmer Name: Akash Deshmukh
 * Purpose: Gallery Controller
 * Date: 02 Sept 2016
 * Dependency: gallerymodel.php
 */

class Gallery extends CI_Controller {
    /*
     * Purpose: Constructor.
     * Date: 02 Sept 2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct() {
        parent::__construct();

        $this->load->model('admin/gallerymodel', 'gallerymodel', TRUE);
    }

    //Function to load the grid view
    public function index() {
        if ($this->session->userdata('user_id')) {
            $data['sliderData'] = $this->gallerymodel->getData();
            $this->template->view('gallery', $data);
        } else {
            redirect('admin', 'refresh');
        }
    }

    //Function for the slider count
    public function slider_count() {
        $cnt = $this->gallerymodel->slider_count();
        echo $cnt;
    }

    //Function for loading the slider state count
    public function slider_state_count() {
        $cnt = $this->gallerymodel->slider_state_count();
        echo $cnt;
    }

    //Function for add and edit operations
    public function addedit($edit_id = 0) {
        if ($this->session->userdata('user_id')) {
            $data = array();
            $data['edit_id'] = $edit_id;
            $formData = array(
                'txttitle' => '',
                'txturl' => '',
                'image' => ''
            );

            if (empty($_POST)) {
                if ($edit_id) {
                    $editData = $this->gallerymodel->getData($edit_id);
                    if ($editData) {
                        $formData = array(
                            'txttitle' => $editData->sli_title,
                            'image' => $editData->sli_image
                        );
                    }
                }
                $data['formData'] = $formData;
                $this->template->view('addgallery', $data);
            } else {
                // process posted data
                $edit_id = $this->input->post('edit_id');
                if ($edit_id) {
                    $file_name = "";
                    if ($_FILES['image']['name'] != '') {
                        $upload_data = $this->upload_slider_image();
                        if (array_key_exists('error', $upload_data)) {
                            $this->session->set_userdata('toast_error_message', $upload_data['error']);
                            redirect('gallery/addedit', 'refresh');
                        } else {
                            echo $upload_data['file_name'];
                            $update_data = array(
                                'sli_title' => mysql_real_escape_string($this->input->post('txttitle')),
                                'sli_image' => mysql_real_escape_string($upload_data['file_name'])
                            );
                            $file_name = $upload_data['file_name'];
                        }
                    } else {
                        $update_data = array(
                            'sli_title' => mysql_real_escape_string($this->input->post('txttitle')),
                        );
                    }

                    $result = $this->gallerymodel->action('update', $update_data, $edit_id);
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

                            $this->session->set_userdata('toast_message', 'Record updated successfully.');
                            $this->session->set_userdata('uploaded_img', $file_name);
                            $this->session->set_userdata('redirect_to', 'admin/gallery');
                            redirect('admin/crop');
                        } else {
                            $this->session->set_userdata('toast_message', 'Record updated successfully.');
                            redirect('admin/gallery');
                        }
                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record.');
                    }
                } else {
                    $maxsequnce = $this->gallerymodel->getMaxSeq();
                    $sTime = date("Y-m-d H:i:s");
                    $insert_data = array(
                        'sli_title' => mysql_real_escape_string($this->input->post('txttitle')),
                        'sli_status' => 1,
                        'sli_sequence' => $maxsequnce,
                        'is_deleted' => 0,
                        'sli_created_on' => $sTime,
                        'sli_modified_on' => $sTime
                    );
                    if ($_FILES['image']['name'] != '') {
                        $upload_data = $this->upload_slider_image();

                        if (array_key_exists('error', $upload_data)) {
                            $this->session->set_userdata('toast_error_message', $upload_data['error']);
                            redirect('admin/gallery/addedit', 'refresh');
                        } else {
                            $insert_data['sli_image'] = $upload_data['file_name'];
                            $file_name = $upload_data['file_name'];
                        }
                    }

                    $result = $this->gallerymodel->action('insert', $insert_data);
                    if ($result) {
                        $this->session->set_userdata('toast_message', 'Record added successfully.');
                        //$this->session->set_userdata('toast_message','Record updated successfully');
                        $this->session->set_userdata('uploaded_img', $file_name);
                        $this->session->set_userdata('redirect_to', 'admin/gallery');
                        redirect('admin/crop');
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

    //Method to upload slider image to the server.
    public function upload_slider_image() {
        if (!file_exists(SLIDER_IMAGE_PATH)) {
            mkdir(SLIDER_IMAGE_PATH, 0700, true);
        }
        $file_name = $this->stripJunk($_FILES['image']['name']); //preg_replace('/[^a-zA-Z0-9_.]/s', '', $_FILES['image']['name']);

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

    //Function to delete slider image 
    public function delete_slider($slider_id = 0) {
        $update_array = array(
            'is_deleted' => 1
        );
        $this->gallerymodel->action('update', $update_array, $slider_id);
        $this->session->set_userdata('toast_message', 'Record deleted successfully.');
        redirect('admin/gallery');
    }

    //Update the slider status
    public function update_slider_status() {
        $sli_id = $this->input->post('sli_id');
        $this->gallerymodel->update_status($sli_id);
    }

    //Function for changing the sequence of the lists
    public function change_sequence($move = 'up', $mnu_id = 0) {
        $this->gallerymodel->change_sequence($mnu_id, $move);
        redirect('admin/gallery');
    }

}

?>