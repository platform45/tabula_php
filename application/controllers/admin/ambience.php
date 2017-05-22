<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH.'controllers/admin/Access.php';


/*
 * Programmer Name: Akash Deshmukh
 * Purpose: Slider Controller
 * Date: 02 Sept 2016
 * Dependency: slidermodel.php
 */

class Ambience extends Access {
    /*
     * Purpose: Constructor.
     * Date: 02 Sept 2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct() {
        parent::__construct();

        $this->load->model('admin/ambiencemodel', 'ambiencemodel', TRUE);
    }

    //To load model
    public function index() {
       
            $data['ambienceData'] = $this->ambiencemodel->getData();
            $this->template->view('ambience', $data);
        
    }

    public function addedit($edit_id = 0) {
       
            $data = array();
            $data['edit_id'] = $edit_id;
            $formData = array(
                'txttitle' => ''
            );

            if (empty($_POST)) {
                if ($edit_id) {
                    $editData = $this->ambiencemodel->getData($edit_id);
                    if ($editData) {
                        $formData = array(
                            'txttitle' => $editData->ambience_name
                        );
                    }
                }
                $data['formData'] = $formData;
                $this->template->view('addambience', $data);
            } else {
                $edit_id = $this->input->post('edit_id');

                if ($edit_id) {
                    $update_data = array(
                        'ambience_name' => $this->input->post('txttitle'),
                    );
                    $result = $this->ambiencemodel->action('update', $update_data, $edit_id);
                    if ($result) {
                        $this->session->set_userdata('toast_message', 'Record updated successfully.');
                        redirect('admin/ambience');
                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record.');
                    }
                } else {

                    $insert_data = array(
                        'ambience_name' => $this->input->post('txttitle'),
                        'status' => '1',
                        'is_deleted' => '0',
                    );


                    $result = $this->ambiencemodel->action('insert', $insert_data);
                    if ($result) {
                        $this->session->set_userdata('toast_message', 'Record added successfully.');

                        redirect('admin/ambience');
                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record.');
                    }
                }
            }
        
    }

      public function check_ambience_exist($ambience_id = 0) {

        $ambience = $this->input->post("txttitle");
        $this->db->select("ambience_id");
        $this->db->where("is_deleted", '0');
        $this->db->where("ambience_name", $ambience);
        if ($ambience_id)
            $this->db->where("ambience_id <>", $ambience_id);
        $result = $this->db->get("ambience");

        if ($result->num_rows() > 0) {
            echo "false";
        } else {
            echo "true";
        }
    }

    // delete slider image 
    public function delete_slider($slider_id = 0) {
        $update_array = array(
            'is_deleted' => '1'
        );
        $this->ambiencemodel->action('update', $update_array, $slider_id);
        $this->session->set_userdata('toast_message', 'Record deleted successfully.');
        redirect('admin/ambience');
    }

    public function update_slider_status() {
        $sli_id = $this->input->post('ambience_id');
        $this->ambiencemodel->update_status($sli_id);
    }

    public function slider_state_count() {
        $cnt = $this->ambiencemodel->slider_state_count();
        echo $cnt;
    }

    public function slider_count() {
        $cnt = $this->ambiencemodel->slider_count();
        echo $cnt;
    }

}

?>