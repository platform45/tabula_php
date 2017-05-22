<?php

/*
 * Programmer Name:Akash Deshmukh
 * Purpose: Countries Model
 * Date: 02 Sept 2016
 * Dependency: countriesmodel.php
 */

class Countries extends CI_Controller {
    /*
     * Purpose: Constructor.
     * Date: 02 Sept 2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct() {
        parent::__construct();

        $this->load->model('admin/countriesmodel', '', TRUE);
    }

    /*
     * Purpose: To Load countries
     * Date: 02 Sept 2016
     * Input Parameter: None
     * Output Parameter: None
     */

    public function index() {

        if ($this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_ADMIN_TYPE || $this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_SUBADMIN_TYPE) {
            $data['page_title'] = SITE_TITLE . 'Country Management';
            $data['countriesData'] = $this->countriesmodel->getData();
            $this->template->view('countries', $data);
        } else {
            redirect('admin', 'refresh');
        }
    }

    /*
     * Purpose: To add/edit role
     * Date: 17 Aug 2016
     * Input Parameter: country_id, country_flag, country_abbrivation, country_name
     * Output Parameter: None
     */

    //Function to add and delete countries
    public function addedit($edit_id = 0) {

        if ($this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_ADMIN_TYPE || $this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_SUBADMIN_TYPE) {
            $data = array();
            $data['page_title'] = SITE_TITLE . ($edit_id ? "Edit" : "Add") . ' Country';
            $data['edit_id'] = $edit_id;
            $formData = array(
                'country_id' => "",
                'country_code' => "",
                'country_abbrivation' => "",
                'country_name' => "",
                'status' => ""
            );

            if (empty($_POST)) {
                if ($edit_id) {
                    $editData = $this->countriesmodel->getData($edit_id);
                    if ($editData) {
                        $formData = array(
                            'country_id' => $editData->cou_id,
                            'country_name' => trim($editData->cou_name),
                            'country_abbrivation' => $editData->cou_abbreviation,
                            'country_code' => $editData->cou_code,
                            'status' => $editData->status
                        );
                    }
                }
                $data['formData'] = $formData;
                $this->template->view('addcountries', $data);
            } else {

                // process posted data
                $edit_id = $this->input->post('edit_id');
                if ($edit_id) {
                    $update_data = array(
                        'cou_name' => $this->input->post('country_name'),
                        'cou_abbreviation' => $this->input->post('country_abbrivation'),
                        'cou_code' => $this->input->post('country_code')
                    );

                    $result = $this->countriesmodel->action('update', $update_data, $edit_id);
                    if ($result) {
                        $this->session->set_userdata('toast_message', 'Record updated successfully.');
                        redirect('admin/countries');
                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record.');
                    }
                } else {

                    $insert_data = array(
                        'cou_name' => $this->input->post('country_name'),
                        'cou_code' => $this->input->post('country_code'),
                        'cou_abbreviation' => $this->input->post('country_abbrivation'),
                        'status' => '1',
                    );
                    $result = $this->countriesmodel->action('insert', $insert_data);
                    if ($result) {
                        $this->session->set_userdata('toast_message', 'Record added successfully.');
                        redirect('admin/countries');
                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record.');
                    }
                }
            }
        } else {
            redirect('admin', 'refresh');
        }
    }

    //Function to delete countries
    public function delete_countries($country_id = 0) {
        $update_array = array();
        $this->countriesmodel->action('delete', $update_array, $country_id);
        $this->session->set_userdata('toast_message', 'Record deleted successfully.');
        redirect('admin/countries', 'refresh');
    }

    //Function to update status
    public function update_status() {
        $country_id = $this->input->post('country_id');
        $changeStatus = $this->input->post('changeStatus');
        $countryData = $this->countriesmodel->getData($country_id);

        if ($changeStatus)
            $changeStatus = 0;
        else
            $changeStatus = 1;
        if ($countryData->region_count > 0 && $changeStatus == 0) {
            echo 0;
            die;
        }
        $update_array = array(
            'status' => "$changeStatus"
        );

        $this->countriesmodel->action('update', $update_array, $country_id);
        echo 1;
    }

    //Function to check existence of country 
    public function check_country_exists($id = 0) {
        $country_name = trim($this->input->post("country_name"));
        $countryPresent = $this->countriesmodel->checkCountry($country_name, $id);

        if ($countryPresent) {
            echo json_encode(FALSE);
        } else {
            echo json_encode(TRUE);
        }
    }

    //Function to check country abbreviation
    public function check_abbr_exists($id = 0) {
        $abbr_name = trim($this->input->post("country_abbrivation"));
        $abbrPresent = $this->countriesmodel->checkCountryAbbr($abbr_name, $id);

        if ($abbrPresent) {
            echo json_encode(FALSE);
        } else {
            echo json_encode(TRUE);
        }
    }

}

?>