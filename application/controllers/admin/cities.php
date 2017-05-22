<?php

/*
 * Programmer Name:Akash Deshmukh
 * Purpose:Display and add cities
 * Date:02 Sept 2016
 * Dependency: citiesmodel.php
 */

class Cities extends CI_Controller {
    /*
     * Purpose: Constructor.
     * Date: 02 Sept 2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct() {
        parent::__construct();

        $this->load->model('admin/citiesmodel', '', TRUE);
        $this->load->model('admin/countriesmodel', '', TRUE);
    }

    /*
     * Purpose: To Load cities
     * Date: 02 Sept 2016
     * Input Parameter: None
     * Output Parameter: None
     */

    public function index() {
        if ($this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_ADMIN_TYPE || $this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_SUBADMIN_TYPE) {
            $data['page_title'] = SITE_TITLE . 'City Management';
            $data['countriesData'] = $this->countriesmodel->getData();
            $this->template->view('cities', $data);
        } else {
            redirect('admin', 'refresh');
        }
    }

    //Function to add and edit
    public function addedit($edit_id = 0) {
        if ($this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_ADMIN_TYPE || $this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_SUBADMIN_TYPE) {
            $data = array();
            $data['page_title'] = SITE_TITLE . ($edit_id ? "Edit" : "Add") . ' City';
            $data['edit_id'] = $edit_id;
            $formData = array(
                'city_id' => "",
                'country_id' => "",
                'region_id' => "",
                'city_name' => ""
            );
            $data['countriesData'] = $this->countriesmodel->getData("", '1');
            $data['regionsData'] = "";

            if (empty($_POST)) {
                if ($edit_id) {
                    $editData = $this->citiesmodel->getData($edit_id);
                    if ($editData) {
                        $formData = array(
                            'city_id' => $editData->city_id,
                            'city_name' => $editData->city_name,
                            'country_id' => $editData->country_id,
                            'region_id' => $editData->region_id,
                            'status' => $editData->status
                        );
                    }
                    $data['regionsData'] = $this->citiesmodel->getRegionByCountry($editData->country_id);
                }
                $data['formData'] = $formData;
                $this->template->view('addcities', $data);
            } else {

                // process posted data
                $edit_id = $this->input->post('edit_id');

                if ($edit_id) {
                    $update_data = array(
                        'city_name' => $this->input->post('city_name'),
                        'country_id' => $this->input->post('country_id'),
                        'region_id' => $this->input->post('region_id'),
                        'status' => '1'
                    );
                    $result = $this->citiesmodel->action('update', $update_data, $edit_id);
                    if ($result) {
                        $this->session->set_userdata('toast_message', 'Record updated successfully.');
                        redirect('admin/cities');
                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record.');
                    }
                } else {

                    $insert_data = array(
                        'city_name' => $this->input->post('city_name'),
                        'country_id' => $this->input->post('country_id'),
                        'region_id' => $this->input->post('region_id'),
                        'status' => '1'
                    );
                    $result = $this->citiesmodel->action('insert', $insert_data);
                    if ($result) {
                        $this->session->set_userdata('toast_message', 'Record added successfully.');
                        redirect('admin/cities');
                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record.');
                    }
                }
            }
        } else {
            redirect('admin', 'refresh');
        }
    }

    //Function to delete cities
    public function delete_cities($city_id = 0) {
        $update_array = array();
        $this->citiesmodel->action('delete', $update_array, $city_id);
        $this->session->set_userdata('toast_message', 'Record deleted successfully.');
        redirect('admin/cities', 'refresh');
    }

    //Function to update status
    public function update_status() {
        $city_id = $this->input->post('city_id');
        $changeStatus = $this->input->post('changeStatus');
        if ($changeStatus)
            $changeStatus = 0;
        else
            $changeStatus = 1;
        $update_array = array(
            'status' => "$changeStatus"
        );
        $this->citiesmodel->action('update', $update_array, $city_id);
        return 1;
    }

    //Function to check cities exists
    public function check_city_exists($id = 0) {
        $where['country_id'] = $this->input->post("country_id");
        $where['region_id'] = $this->input->post("region_id");
        $where['city_name'] = trim($this->input->post("city_name"));
        $cityPresent = $this->citiesmodel->checkCity($where, $id);

        if ($cityPresent) {
            echo json_encode(FALSE);
        } else {
            echo json_encode(TRUE);
        }
    }

    //Function to get region by country
    public function get_region_by_country() {
        $country_id = $this->input->post("country_id");
        $isList = $this->input->post("isList");
        $regionPresent = $this->citiesmodel->getRegionByCountry();

        if ($regionPresent) {
            $option_list = "";
            $option_list.= "<option value=''>Select State</option>";
            foreach ($regionPresent as $val) {
                if ($isList == "Y")
                    $option_list.= "<option value='" . $val['region_name'] . "' data-state-id='" . $val['region_id'] . "'>" . htmlentities(stripslashes($val['region_name'])) . "</option>";
                else
                    $option_list.= "<option value='" . $val['region_id'] . "' data-state-id='" . $val['region_id'] . "'>" . htmlentities(stripslashes($val['region_name'])) . "</option>";
            }
            echo $option_list;
        } else {
            echo json_encode(FALSE);
        }
    }

    //Function to get region by country
    public function get_region_id_by_country() {
        $country_id = $this->input->post("country_id");
        $regionPresent = $this->citiesmodel->getRegionByCountry($country_id);

        if ($regionPresent) {
            $option_list = "";
            $option_list.= "<option value=''>Select State</option>";
            foreach ($regionPresent as $val) {
                $option_list.= "<option value='" . $val['region_id'] . "'>" . htmlentities(stripslashes($val['region_name'])) . "</option>";
            }
            echo $option_list;
        } else {
            echo json_encode(FALSE);
        }
    }

    //Function to get city by region
    public function get_city_by_region() {
        $region_id = $this->input->post("region_id");
        if ($region_id) {
            $cityPresent = $this->citiesmodel->getCityByRegion($region_id);
            if ($cityPresent) {
                $option_list = "";
                $option_list.= "<option value=''>Select City</option>";
                foreach ($cityPresent as $val) {
                    $option_list.= "<option value='" . $val['city_id'] . "'>" . htmlentities(stripslashes($val['city_name'])) . "</option>";
                }
                echo $option_list;
            } else {
                echo json_encode(FALSE);
            }
        }
    }

    /*
     * Purpose: Load City with Ajax with datatable JS
     * Date: 12 Sept 2016
     * Input Parameter:
     */

    public function city_listing() {

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

        $sSearchCountryName = $aPostArray['columns'][3]['search']['value']; /*         * *** SEARCH BY Country NAME **** */
        $sSearchStateName = $aPostArray['columns'][2]['search']['value']; /*         * *** SEARCH BY state NAME **** */
        $sSearchCityName = $aPostArray['columns'][1]['search']['value']; /*         * *** SEARCH BY CITY NAME **** */


        //----- SEARCH ARRAY -----//
        $aSearchArray = array($sSearchCountryName, $sSearchStateName, $sSearchCityName);

        ##======== ORDER BY CONDITION ========##
        $iOrderByColumn = $aPostArray['order'][0]['column'];
        switch ($iOrderByColumn) {
            case 0: $sColumnName = 'city_id';
                break;
            case 1: $sColumnName = 'city_name';
                break;
            case 2: $sColumnName = 'status';
                break;
            default :
        }
        $sOrderBy = $aPostArray['order'][0]['dir'];
        //----- SEARCH ARRAY -----//
        $aOrderByCondition = array('colomn_name' => $sColumnName, 'order_by' => $sOrderBy);

        $aOrderCount = $this->citiesmodel->CityCount($aSearchArray);

        $aOrderListing = $this->citiesmodel->CityListing($iPageSize, $iRecordStartFrom, $aSearchArray, $aOrderByCondition);

        $iOrderCount = $aOrderCount[0]['NumberOfCities'];

        if ($iOrderCount > 0) {

            foreach ($aOrderListing as $iKey => $aVal) {
                $aDataTableResponce[$iKey] = array();

                //------- ORDER DETAIL PAGE LINK --------//
                $sDetailLink = '<a href="n">link</a>';

                // Prepare status icon
                $status = "<a href='#myStatus' role='button' data-toggle='modal' id='" . $aVal['city_id'] . "' class='status' data-status='" . $aVal['status'] . "'><span id='status" . $aVal['city_id'] . "' class='status_icon'>";

                if ($aVal['status'] == 0):
                    $status .= "<i class='fa fa-ban fa-2x' title='Inactive'></i>";
                else:
                    $status .= "<i class='fa fa-check fa-2x' title='Active'></i>";
                endif;

                $status .="</span></a>";

                // Prepare edit link
                $edit_link = "<a href='" . $this->config->item('admin_url') . 'cities/addedit/' . $aVal['city_id'
                        ] . "' title='Edit'><i class='fa fa-pencil-square-o fa-2x'></i></a>";

                // Prepare delete icon
                $delete_link = "<a href='#myModal' class='delete_button' id='" . $aVal['city_id'] . "' faq='button' data-toggle='modal' title='Delete'><i class='fa fa-times-circle-o fa-2x' ></i></a>";

                array_push($aDataTableResponce[$iKey], $iKey + 1 + $iRecordStartFrom);
                array_push($aDataTableResponce[$iKey], $aVal['city_name']);
                array_push($aDataTableResponce[$iKey], $aVal['region_name']);
                array_push($aDataTableResponce[$iKey], $aVal['cou_name']);
                array_push($aDataTableResponce[$iKey], $status);
                array_push($aDataTableResponce[$iKey], $edit_link);
                array_push($aDataTableResponce[$iKey], $delete_link);
            }
        }
        else {
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