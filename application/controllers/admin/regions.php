<?php

/*
 * Programmer Name:Akash Deshmukh
 * Purpose:To add regions specific to the restaurant.
 * Date: 02 Sept 2016
 * Dependency: regionsmodel.php
 */

class Regions extends CI_Controller {
    /*
     * Purpose: Constructor.
     * Date:  02 Sept 2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct() {
        parent::__construct();
        $this->load->model('admin/regionsmodel', '', TRUE);
        $this->load->model('admin/countriesmodel', '', TRUE);
    }

    /*
     * Purpose: To Load Region
     * Date:  02 Sept 2016
     * Input Parameter: None
     * Output Parameter: None
     */

    public function index() {

        if ($this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_ADMIN_TYPE || $this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_SUBADMIN_TYPE) {
            $data['page_title'] = SITE_TITLE . 'Region Management';
            $data['countriesData'] = $this->countriesmodel->getData();
            $this->template->view('regions', $data);
        } else {
            redirect('admin', 'refresh');
        }
    }

    //Function to add, edit and delete the regions
    public function addedit($edit_id = 0) {
        if ($this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_ADMIN_TYPE || $this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_SUBADMIN_TYPE) {
            $data = array();
            $data['page_title'] = SITE_TITLE . ($edit_id ? "Edit" : "Add") . ' Region';
            $data['edit_id'] = $edit_id;
            $formData = array(
                'country' => "",
                'region_name' => ""
            );
            if (empty($_POST)) {
                if ($edit_id) {
                    $editData = $this->regionsmodel->getData($edit_id);
                    if ($editData) {
                        $formData = array(
                            'region_id' => $editData->region_id,
                            'region_name' => trim($editData->region_name),
                            'country' => $editData->cou_id,
                            'status' => $editData->status
                        );
                    }
                }
                $data['countriesData'] = $this->countriesmodel->getData();
                // print_r($data['countriesData']);die;
                $data['formData'] = $formData;
                $this->template->view('addregions', $data);
            } else {
                // process posted data
                $edit_id = $this->input->post('edit_id');
                if ($edit_id) {
                    $update_data = array(
                        'region_name' => $this->input->post('region_name'),
                        'cou_id' => $this->input->post('country'),
                        'status' => '1'
                    );
                    $result = $this->regionsmodel->action('update', $update_data, $edit_id);
                    if ($result) {
                        $this->session->set_userdata('toast_message', 'Record updated successfully.');
                        redirect('admin/regions');
                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record.');
                    }
                } else {
                    $insert_data = array(
                        'region_name' => $this->input->post('region_name'),
                        'cou_id' => $this->input->post('country'),
                        'status' => '1'
                    );
                    $result = $this->regionsmodel->action('insert', $insert_data);
                    if ($result) {
                        $this->session->set_userdata('toast_message', 'Record added successfully.');
                        redirect('admin/regions');
                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record.');
                    }
                }
            }
        } else {
            redirect('admin', 'refresh');
        }
    }

    //Function to delete regions
    public function delete_regions($region_id = 0) {
        $update_array = array();
        $regionData = $this->regionsmodel->getData($region_id);
        $this->regionsmodel->action('delete', $update_array, $region_id);
        $this->session->set_userdata('toast_message', 'Record deleted successfully.');
        redirect('admin/regions', 'refresh');
    }

    //Function to update status
    public function update_status() {
        $region_id = $this->input->post('region_id');
        $changeStatus = $this->input->post('changeStatus');
        $regionData = $this->regionsmodel->getData($region_id);

        if ($changeStatus)
            $changeStatus = 0;
        else
            $changeStatus = 1;

        if ($regionData->city_count > 0 && $changeStatus == 0) {
            echo 0;
            die;
        }
        $update_array = array(
            'status' => "$changeStatus"
        );
        $this->regionsmodel->action('update', $update_array, $region_id);
        echo 1;
    }

    //Function to check existence of region
    public function check_region_exists($id = 0) {

        $country_id = $this->input->post("country_id");
        $region_name = trim($this->input->post("region_name"));
        $regionPresent = $this->regionsmodel->checkregion($region_name, $country_id, $id);

        if ($regionPresent) {
            echo json_encode(FALSE);
        } else {
            echo json_encode(TRUE);
        }
    }

    /*
     * Purpose: Load Region with Ajax with datatable JS
     * Date: 12 Sept 2016
     * Input Parameter:
     */

    public function region_listing() {

        //---- DataTable Draw -----//
        $iDraw = $this->input->post('draw');

        //---- DataTable Length: Number of record on one page -----//
        $iLength = $this->input->post('length');

        //---- DataTable Start: Start record from -----//
        $iRecordStartFrom = $this->input->post('start');

        $iPageSize = $iLength; #=== ONE PAGE RECORDS

        /*         * **********************************
         * POST ARRAY WITH SEARCH VALUES AND
         * ORDER BY WITH COLOMN POSITION.
         * *** */
        ##======== SEARCH CONDITION ========##
        $aPostArray = $this->input->post(); /*         * *** POST ARRAY **** */

        $sSearchCountryName = $aPostArray['columns'][2]['search']['value']; /*         * *** SEARCH BY Country NAME **** */
        $sSearchCityName = $aPostArray['columns'][1]['search']['value']; /*         * *** SEARCH BY CITY NAME **** */


        //----- SEARCH ARRAY -----//
        $aSearchArray = array($sSearchCityName, $sSearchCountryName);

        ##======== ORDER BY CONDITION ========##
        $iOrderByColumn = $aPostArray['order'][0]['column'];
        switch ($iOrderByColumn) {
            case 0: $sColumnName = 'region_id';
                break;
            case 1: $sColumnName = 'region_name';
                break;
            case 2: $sColumnName = 'status';
                break;
            default :
        }
        $sOrderBy = $aPostArray['order'][0]['dir'];
        //----- SEARCH ARRAY -----//
        $aOrderByCondition = array('colomn_name' => $sColumnName, 'order_by' => $sOrderBy);

        $aOrderCount = $this->regionsmodel->RegionCount($aSearchArray);

        $aOrderListing = $this->regionsmodel->RegionListing($iPageSize, $iRecordStartFrom, $aSearchArray, $aOrderByCondition);

        $iOrderCount = $aOrderCount[0]['NumberOfRegions'];

        if ($iOrderCount > 0) {

            foreach ($aOrderListing as $iKey => $aVal) {
                $aDataTableResponce[$iKey] = array();

                $aVal['city_count'] = $this->regionsmodel->getCityCountByRegion($aVal['region_id']);


                //------- ORDER DETAIL PAGE LINK --------//
                $sDetailLink = '<a href="n">link</a>';

                // Prepare status icon
                $status = "<a ";
                if ($aVal['city_count'] > 0 && $aVal['status'] == 1) {
                    $status .= "class='errorStatus grey'";
                } else {
                    $status .= "data-toggle='modal' class='status'";
                }
                $status .= "href='#myStatus' role='button' id='" . $aVal['region_id'] . "'  data-status='" . $aVal['status'] . "'><span id='status" . $aVal['region_id'] . "' class='status_icon'>";

                if ($aVal['status'] == 0):
                    $status .= "<i class='fa fa-ban fa-2x' title='Inactive'></i>";
                else:
                    $status .= "<i class='fa fa-check fa-2x' title='Active'></i>";
                endif;

                $status .="</span></a>";

                // Prepare edit link
                $edit_link = "<a href='" . $this->config->item('admin_url') . 'regions/addedit/' . $aVal['region_id'
                        ] . "' title='Edit'><i class='fa fa-pencil-square-o fa-2x'></i></a>";


                $delete_link = "<a ";
                if ($aVal['city_count'] > 0) {
                    $delete_link .= "class='errorDelete grey'";
                } else {
                    $delete_link .= "data-toggle='modal' class='delete_button'";
                }
                // Prepare delete icon
                $delete_link .= " href='#myModal' id='" . $aVal['region_id'] . "' faq='button'  title='Delete'><i class='fa fa-times-circle-o fa-2x' ></i></a>";

                array_push($aDataTableResponce[$iKey], $iKey + 1 + $iRecordStartFrom);
                array_push($aDataTableResponce[$iKey], $aVal['region_name']);
                array_push($aDataTableResponce[$iKey], $aVal['cou_name']);
                array_push($aDataTableResponce[$iKey], $status);
                array_push($aDataTableResponce[$iKey], $edit_link);
                array_push($aDataTableResponce[$iKey], $delete_link);
            }
        } else {
            $aDataTableResponce = "";
        }

        if (!empty($aDataTableResponce) && count($aDataTableResponce) > 0) {
            echo json_encode(array("draw" => $iDraw, "recordsTotal" => $iOrderCount, "recordsFiltered" => $iOrderCount, "data" => $aDataTableResponce));
        } else {
            echo json_encode(array("recordsTotal" => 0, "recordsFiltered" => 0, "data" => ''));
        }
    }

}

?>