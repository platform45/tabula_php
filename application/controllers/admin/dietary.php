<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH.'controllers/admin/Access.php';
/*
 * Programmer Name: Akash Deshmukh
 * Purpose: Dietary Controller
 * Date: 02 Sept 2016
 * Dependency: Dietarymodel.php
 */

class Dietary extends Access {
    /*
     * Purpose: Constructor.
     * Date: 02 Sept 2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct() {
        parent::__construct();

        $this->load->model('admin/dietarymodel', 'dietarymodel', TRUE);
    }

    //To load model
    public function index() {
        
            $data['dietaryData'] = $this->dietarymodel->getData();
            $this->template->view('dietary', $data);
        
    }

    public function addedit($edit_id = 0) {
       
            $data = array();
            $data['edit_id'] = $edit_id;
            $formData = array(
                'txttitle' => ''
            );

            if (empty($_POST)) {
                if ($edit_id) {
                    $editData = $this->dietarymodel->getData($edit_id);
                    if ($editData) {
                        $formData = array(
                            'txttitle' => $editData->diet_preference
                        );
                    }
                }
                $data['formData'] = $formData;
                $this->template->view('adddietary', $data);
            } else {
                $edit_id = $this->input->post('edit_id');

                if ($edit_id) {
                    $update_data = array(
                        'diet_preference' => $this->input->post('txttitle'),
                    );
                    $result = $this->dietarymodel->action('update', $update_data, $edit_id);
                    if ($result) {
                        $this->session->set_userdata('toast_message', 'Record updated successfully.');
                        redirect('admin/dietary');
                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record.');
                    }
                } else {

                    $insert_data = array(
                        'diet_preference' => $this->input->post('txttitle'),
                        'is_active' => 1,
                        'is_deleted' => 0,
                    );


                    $result = $this->dietarymodel->action('insert', $insert_data);
                    if ($result) {
                        $this->session->set_userdata('toast_message', 'Record added successfully.');

                        redirect('admin/dietary');
                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record.');
                    }
                }
            }
        
    }


    public function check_dietary_exist($diet_id = 0) {

        $dietary = $this->input->post("txttitle");
        $this->db->select("diet_id");
        $this->db->where("is_deleted", '0');
        $this->db->where("diet_preference", $dietary);
        if ($diet_id)
            $this->db->where("diet_id <>", $diet_id);
        $result = $this->db->get("dietary_preference");

        if ($result->num_rows() > 0) {
            echo "false";
        } else {
            echo "true";
        }
    }

    // delete dietary preference 
    public function delete_dietary($diet_id = 0) {
        $update_array = array(
            'is_deleted' => 1
        );
        $this->dietarymodel->action('update', $update_array, $diet_id);
        $this->session->set_userdata('toast_message', 'Record deleted successfully.');
        redirect('admin/dietary');
    }

    public function update_dietary_status() {
        $diet_id = $this->input->post('diet_id');
        $this->dietarymodel->update_status($diet_id);
    }

    public function dietary_state_count() {
        $cnt = $this->dietarymodel->dietary_state_count();
        echo $cnt;
    }

    public function dietary_count() {
        $cnt = $this->dietarymodel->dietary_count();
        echo $cnt;
    }

}

?>