<?php

/*
 * Programmer Name:SK
 * Purpose:Role Controller
 * Date:19 Dec 2014
 * Dependency: adsmodel.php
 */

class Ads extends CI_Controller {
    /*
     * Purpose: Constructor.
     * Date: 19 Dec 2014
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct() {
        parent::__construct();

        $this->load->model('admin/Adsmodel', 'adsmodel', TRUE);
    }

    /*
     * Purpose: To Load role
     * Date: 19 Dec 2014
     * Input Parameter: None
     * Output Parameter: None
     */

    public function index() {
        if ($this->session->userdata('user_id')) {
            $data['adsData'] = $this->adsmodel->getData();
            $this->template->view('ads', $data);
        } else {
            redirect('admin', 'refresh');
        }
    }

    public function ad_count() {
        $cnt = $this->adsmodel->ads_count();
        echo $cnt;
    }

    public function addedit($edit_id = 0) {


        if ($this->session->userdata('user_id')) {
            $data = array();
            $data['edit_id'] = $edit_id;
            $data['channelArray'] = $this->adsmodel->getChannels();

            $formData = array(
                'ads' => '',
                'channel_id' => '',
                'ad_title' => '',
                'ad_descirption' => '',
                'ad_video' => '',
            );

            if (empty($_POST)) {
                if ($edit_id) {
                    $editData = $this->adsmodel->getData($edit_id);
                    // print_r($editData);die;

                    if ($editData) {
                        $formData = array(
                            'ad_id' => $editData->ad_id,
                            'channel_id' => $editData->channel_id,
                            'ad_title' => $editData->ad_title,
                            'ad_descirption' => $editData->ad_description,
                            'ad_video' => $editData->ad_video,
                        );
                    }
                }
                $data['formData'] = $formData;
                $this->template->view('addads', $data);
            } else {
                // process posted data
                $edit_id = $this->input->post('edit_id');
                if ($edit_id) {
                    $file_name = "";
                    if ($_FILES['video']['name'] != '') {
                        $upload_data = $this->upload_vedio();
                        if (array_key_exists('error', $upload_data)) {
                            $this->session->set_userdata('toast_error_message', $upload_data['error']);
                            redirect('admin/ads/addedit/' . $edit_id, 'refresh');
                        } else {

                            $update_data = array(
                                'ad_title' => $this->input->post('ad_title'),
                                'ad_description' => $this->input->post('ad_descirption'),
                                'ad_video' => $upload_data['file_name'],
                                'channel_id' => $this->input->post('channel_id')
                            );
                            $file_name = $upload_data['file_name'];
                        }
                    } else {


                        $update_data = array(
                            'ad_title' => $this->input->post('ad_title'),
                            'ad_description' => $this->input->post('ad_descirption'),
                            'channel_id' => $this->input->post('channel_id')
                        );
                    }

                    $result = $this->adsmodel->action('update', $update_data, $edit_id);




                    if ($result) {

                        $this->session->set_userdata('toast_message', 'Record updated successfully.');
                        redirect('admin/ads');
                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record.');
                    }
                } else {

                    $maxsequnce = $this->adsmodel->getMaxSeq();

                    $insert_data = array(
                        'ad_title' => $this->input->post('ad_title'),
                        'ad_description' => $this->input->post('ad_descirption'),
                        'channel_id' => $this->input->post('channel_id')
                    );

                    if ($_FILES['video']['name'] != '') {
                        // print_r($_FILES['video']['name']);
                        $upload_data = $this->upload_vedio();
                        if (array_key_exists('error', $upload_data)) {
                            $this->session->set_userdata('toast_error_message', $upload_data['error']);
                            redirect('admin/video/addedit', 'refresh');
                        } else {
                            $insert_data['ad_video'] = $upload_data['file_name'];
                            $file_name = $upload_data['file_name'];
                        }
                    }

                    $result = $this->adsmodel->action('insert', $insert_data);



                    if ($result) {
                        $this->session->set_userdata('toast_message', 'Record added successfully.');
                        //$this->session->set_userdata('toast_message','Record updated successfully');
                        $this->session->set_userdata('uploaded_img', $file_name);
                        $this->session->set_userdata('redirect_to', 'admin/ads');

                        redirect('admin/ads');
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

    /*
     * Purpose: Method to upload products image to the server.
     * Date: 12 Oct 2014
     * Input Parameter: None
     *  Output Parameter: 
     *  TRUE : if image upload succeeds.
     *  FALSE : if image upload fails.
     */

    public function upload_vedio() {
        if (!file_exists(ADS_PATH)) {
            mkdir(ADS_PATH, 0755, true);
        }
        $file_name = $this->stripJunk($_FILES['video']['name']); //preg_replace('/[^a-zA-Z0-9_.]/s', '', $_FILES['image']['name']);

        $config = array(
            'upload_path' => ADS_PATH,
            'overwrite' => FALSE,
            'allowed_types' => "flv|mp4|avi|mpeg",
            'max_size' => MAX_UPOAD_VIDEO_SIZE,
            'max_height' => "2160",
            'max_width' => "4096",
            'file_name' => $file_name
        );
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('video')) {
            $upload_data = $this->upload->data();
            $data = array('file_name' => $upload_data['file_name']);
            return $data;
        } else {
            $error = array('error' => $this->upload->display_errors());
            return $error;
        }
    }

    // delete products image 

    public function update_ad_status() {
        $ad_id = $this->input->post('ad_id');
        $this->adsmodel->update_status($ad_id);
    }

    public function delete_ad($video_id = 0) {
        $update_array = array(
            'is_deleted' => 1
        );
        $this->adsmodel->action('update', $update_array, $video_id);
        $this->session->set_userdata('toast_message', 'Record deleted successfully.');
        redirect('admin/ads');
    }

    public function change_sequence($move = 'up', $mnu_id = 0) {
        $this->adsmodel->change_sequence($mnu_id, $move);
        redirect('products/getFeatureData');
    }

    public function getcategories() {
        $channel_id = $this->input->post("channel_id");
        $categoryArray = $this->adsmodel->getCategories($channel_id);
        $option = "<option value='0'>Select category</option>";
        foreach ($categoryArray as $result) {
            $option .= "<option value='" . $result['cat_id'] . "'>" . $result['category_name'] . "</option>";
        }
        echo $option;
        die;
    }

    public function check_title_exists($id = FALSE) {
        $channel_id = $this->input->post('channel_id');

        if ($id === FALSE) {

            $title = $this->input->post('title');
            if ($title != '') {
                $url_exists = $this->adsmodel->check_title_exists($title, $channel_id);
                if ($url_exists && (strcmp($url_exists, $title) != 0))
                    echo json_encode(TRUE);
                else
                    echo json_encode(FALSE);
            }
            else
                echo json_encode(TRUE);
        }
        else {

            $title = $this->input->post('title');
            if ($title != '') {
                $url_exists = $this->adsmodel->check_title_exists($title, $id, $channel_id);
                if ($url_exists && (strcmp($url_exists, $title) != 0))
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