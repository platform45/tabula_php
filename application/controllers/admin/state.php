<?php

/*
 * Programmer Name:Rishi
 * Purpose:Role Controller
 * Date:17 Aug 2016
 * Dependency: regionsmodel.php
 */

class State extends CI_Controller {
    /*
     * Purpose: Constructor.
     * Date: 17 Aug 2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct() {
        parent::__construct();

        $this->load->model('admin/statemodel', '', TRUE);
        $this->load->model('admin/countriesmodel', '', TRUE);
    }

    /*
     * Purpose: To Load Region
     * Date: 17 Aug 2016
     * Input Parameter: None
     * Output Parameter: None
     */

    public function index() {

        if ($this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_ADMIN_TYPE) {
            $data['page_title'] = SITE_TITLE . 'State';
            $data['regionsData'] = $this->statemodel->getData();
            $this->template->view('state', $data);
        } else {
            redirect('admin', 'refresh');
        }
    }

    /*
     * Purpose: To add/edit role
     * Date: 17 Aug 2016
     * Input Parameter: None
     * Output Parameter: None
     */

    public function addedit($edit_id = 0) {
        if ($this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_ADMIN_TYPE) {
            $data = array();
            $data['page_title'] = SITE_TITLE . ($edit_id ? "Edit" : "Add") . ' State';
            $data['edit_id'] = $edit_id;
            $formData = array(
                'state_id' => "",
                'country_id' => "",
                'state_name' => "",
                'status' => ""
            );

            if (empty($_POST)) {
                if ($edit_id) {
                    $editData = $this->statemodel->getData($edit_id);
                    if ($editData) {
                        $formData = array(
                            'state_id' => $editData->state_id,
                            'state_name' => trim($editData->state_name),
                            'country_id' => $editData->state_id,
                            'status' => $editData->status
                        );
                    }
                }
                $data['countriesData'] = $this->countriesmodel->getData();
//                print_r($data['countriesData']);die;
                $data['formData'] = $formData;
                $this->template->view('addstate', $data);
            } else {

                // process posted data
                $edit_id = $this->input->post('edit_id');

                if ($edit_id) {
                    $update_data = array(
                        'state_name' => mysql_real_escape_string(trim($this->input->post('state_name'))),
                        'country_id' => mysql_real_escape_string($this->input->post('country_id')),
                        'status' => mysql_real_escape_string($this->input->post('status'))
                    );
                    $result = $this->statemodel->action('update', $update_data, $edit_id);
                    if ($result) {
                        $this->session->set_userdata('toast_message', 'Record updated successfully.');
                        redirect('admin/state');
                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record');
                    }
                } else {

                    $insert_data = array(
                        'state_name' => mysql_real_escape_string(trim($this->input->post('state_name'))),
                        'country_id' => mysql_real_escape_string($this->input->post('country_id')),
                        'status' => '1'
                    );
                    $result = $this->statemodel->action('insert', $insert_data);

                    if ($result) {
                        $this->session->set_userdata('toast_message', 'Record added successfully.');
                        redirect('admin/state');
                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record.');
                    }
                }
            }
        } else {
            redirect('admin', 'refresh');
        }
    }

    public function delete_regions($region_id = 0) {
        $update_array = array();
        $regionData = $this->statemodel->getData($region_id);
        $this->statemodel->action('delete', $update_array, $region_id);
        $this->session->set_userdata('toast_message', 'Record deleted successfully.');
        redirect('admin/state', 'refresh');
    }

    public function update_status() {
        $region_id = $this->input->post('state_id');
        $changeStatus = $this->input->post('changeStatus');
        $regionData = $this->statemodel->getData($region_id);

        if ($changeStatus)
            $changeStatus = 0;
        else
            $changeStatus = 1;

        if ($regionData->city_count > 0 && $changeStatus == 0) {
            //Cant Deactivate region status if city are associated with it
            echo 0;
            die;
        }

        $update_array = array(
            'status' => "$changeStatus"
        );

        $this->statemodel->action('update', $update_array, $region_id);
        echo 1;
    }

    public function check_region_exists($id = 0) {

        $country_id = $this->input->post("country_id");
        $region_name = trim($this->input->post("state_name"));
        $regionPresent = $this->statemodel->checkregion($region_name, $country_id, $id);

        if ($regionPresent) {
            echo json_encode(FALSE);
        } else {
            echo json_encode(TRUE);
        }
    }

}

?>