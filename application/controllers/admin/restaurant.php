<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH . 'controllers/admin/Access.php';

/*
 * Programmer Name: Akash Deshmukh
 * Purpose:Restaurant Controller
 * Date:02-09-2016
 * Dependency: restaurantmodel.php
 */

class Restaurant extends Access
{
    /*
     * Purpose: Constructor.
     * Date: 02-09-2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/restaurantmodel', 'restaurantmodel', TRUE);
        $this->load->model('admin/countriesmodel', '', TRUE);
        $this->load->model('admin/regionsmodel', '', TRUE);
        $this->load->model('admin/citiesmodel', '', TRUE);
        $this->load->model('admin/cuisinemodel', '', TRUE);
        $this->load->model('admin/ambiencemodel', '', TRUE);
        $this->load->model('admin/dietarymodel', '', TRUE);
        $this->load->model('webservices/bookingmodel', 'bookingmodel', TRUE);
        $this->load->model('webservices/usermodel', 'usermodel', TRUE);
    }

    /*
     * Purpose: To Load restaurant
     * Date: 02-09-2016
     * Input Parameter: None
     * Output Parameter: None
     */

    public function index()
    {

        $data['members'] = $this->restaurantmodel->getData();
        $this->template->view('restaurant', $data);

    }

    //Function to add and edit members
    public function addedit($edit_id = 0)
    {

        $data = array();
        $data['edit_id'] = $edit_id;
        $formData = array(
            'txtrname' => '',
            'txtemail' => '',
            'description' => '',
            'country_id' => "",
            'region_id' => "",
            'resownername' => "",
            'city_id' => "",
            'txtcontact' => "",
            'avgspend' => "",
            'latitude' => "",
            'longitude' => "",
            'address' => "",
            'new_password' => '',
            'conf_password' => '',
            'new_img' => '',
            'hero_image' => '',
            'web_domain' => ''
        );
        $data['countriesData'] = $this->countriesmodel->getData("", '1');
        $data['cuisineData'] = $this->cuisinemodel->getDataRestaurant();
        $data['ambienceData'] = $this->ambiencemodel->getDataRestaurant();
        $data['dietaryData'] = $this->dietarymodel->getDataRestaurant();
        $data['restaurantTimeData'] = $this->restaurantmodel->get_restaurant_time($edit_id);
        $data['time_slots'] = $this->bookingmodel->get_all_time_slot();
        $data['regionsData'] = "";
        $data['citiesData'] = "";

        if (empty($_POST)) {
            //Add Restaurant
            if ($edit_id) {
                $editData = $this->restaurantmodel->getData($edit_id);
                if ($editData) {
                    $formData = array(
                        'txtrname' => $editData->user_first_name,
                        'txtemail' => $editData->user_email,
                        'resownername' => $editData->restaurant_owner_name,
                        'country_id' => $editData->cou_id,
                        'region_id' => $editData->region_id,
                        'city_id' => $editData->city_id,
                        'txtcontact' => $editData->user_contact,
                        'description' => $editData->user_description,
                        'avgspend' => $editData->average_spend,
                        'latitude' => $editData->latitude,
                        'longitude' => $editData->longitude,
                        'address' => $editData->street_address1,
                        'new_img' => $editData->user_image,
                        'hero_image' => $editData->restaurant_hero_image,
                        'web_domain' => $editData->web_domain
                    );
                    $data['regionsData'] = $this->citiesmodel->getRegionByCountry($editData->cou_id);
                    $data['citiesData'] = $this->citiesmodel->getCityByRegion($editData->region_id);


                }
            }
            $aRestaurantDetails = $this->restaurantmodel->getlist($edit_id);
            $dietDetails = $this->restaurantmodel->get_diet_list($edit_id);
            $timedayDetails = $this->restaurantmodel->get_restaurant_time($edit_id);

            $aCuisine = array();
            $aAmbience = array();
            $aDietary = array();
            $atime = array();
            if ($dietDetails) {
                foreach ($dietDetails as $diet) {
                    $aDietary[] = $diet['diet_id'];
                }
            }
            if ($aRestaurantDetails) {
                foreach ($aRestaurantDetails as $aVal) {

                    if ($aVal['rca_type'] == 1) {
                        $aCuisine[] = $aVal['rca_cuisine_ambience_id'];
                    } elseif ($aVal['rca_type'] == 2) {
                        $aAmbience[] = $aVal['rca_cuisine_ambience_id'];
                    }
                }
            }
            if ($timedayDetails) {
                foreach ($timedayDetails as $timeday) {
                    $atime[] = $timeday['open_close_day'];
                }
            }

            $data['aCuisine'] = array_values(array_unique($aCuisine));
            $data['aAmbience'] = array_values(array_unique($aAmbience));
            $data['aDietary'] = array_values(array_unique($aDietary));
            $data['atime'] = array_values(array_unique($atime));
            $data['formData'] = $formData;
            $this->template->view('addrestaurant', $data);
        } else {
            if (!empty($_POST['txtrname']) && !empty($_POST['txtemail'])) {

                // We define our address
                $city_name = $this->citiesmodel->get_city_name($this->input->post('city_id'));
                $state_name = $this->regionsmodel->get_state_name($this->input->post('region_id'));
                $country_name = $this->countriesmodel->get_country_name($this->input->post('country_id'));

                $address = $this->input->post('address') . " " . $city_name . " " . $state_name . " " . $country_name;
                // We get the JSON results from this request
                $geo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyB6UAgO73ZvZx_ZumKDoxuBw7ZWFeWZwxc&address=' . urlencode($address) . '&sensor=false');
                // We convert the JSON to an array

                $geo = json_decode($geo, true);

                // If everything is cool
                if ($geo['status'] == 'OK') {
                    // We set our values
                    $latitude = $geo['results'][0]['geometry']['location']['lat'];
                    $longitude = $geo['results'][0]['geometry']['location']['lng'];
                } else {
                    $this->session->set_userdata('toast_error_message', 'Address is incorrect.');
                    redirect('admin/restaurant/addedit/' . $edit_id, 'refresh');
                }

                $edit_id = $this->input->post('edit_id');

                $file_name = "";
                if (!empty($_FILES['image']['name'])) {
                    $upload_data = $this->upload_members_image();
                    $file_name = $upload_data['file_name'];
                }

                $hero_image = "";
                if (!empty($_FILES['h_image']['name'])) {
                    $upload_data = $this->upload_restaurant_hero_image();
                    $hero_image = $upload_data['file_name'];
                }

                if ($edit_id) {
//update record
                    $update_data = array(
                        'user_first_name' => trim($this->input->post('txtrname')),
                        'user_email' => $this->input->post('txtemail'),
                        'user_contact' => $this->input->post('txtcontact'),
                        'user_description' => trim($this->input->post('description')),
                        'average_spend' => $this->input->post('avgspend'),
                        'restaurant_owner_name' => trim($this->input->post('resownername')),
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'bank_name' => $this->input->post('bankname'),
                        'bank_account_number' => $this->input->post('bankaccnum'),
                        'bank_branch_number' => $this->input->post('bankbrnum'),
                        'bank_account_holder_name' => $this->input->post('bankaccholdername'),
                        'country_id' => 47,
                        'region_id' => $this->input->post('region_id'),
                        'city_id' => $this->input->post('city_id'),
                        'street_address1' => trim($this->input->post('address')),
                        'web_domain' => trim($this->input->post('txtrwebdomain'))
                    );
                    $userPwd = hash('SHA256', $_POST['conf_password']);
                    $userImg = $this->input->post('new_img');
                    $heroImg = $this->input->post('hero_image');
                    $cuisine = $this->input->post('cusine');
                    $ambience = $this->input->post('ambience');
                    $dietary = $this->input->post('dietary');
                    if (!empty($_POST['conf_password'])) {
                        $update_data['user_password'] = $userPwd;
                    }

                    if (!empty($hero_image) && !empty($file_name)) {
                        $update_data['restaurant_hero_image'] = $hero_image;
                        $update_data['user_image'] = $file_name;
                        $result = $this->restaurantmodel->action('update', $update_data, $edit_id);
                        $id = $result;

                        $res = $this->restaurantmodel->update_restaurant_time1($id);
                        for ($i = 1; $i <= 7; $i++) {
                            $day = $this->input->post('day_' . $i);
                            $open_time = $this->input->post('open_from_' . $i);
                            $close_time = $this->input->post('closed_to_' . $i);
                            if (!empty($day)) {
                                if (!empty($open_time) && !empty($close_time)) {
                                    $adata = array(
                                        'open_time_from' => $open_time,
                                        'close_time_to' => $close_time,
                                        'open_close_status' => ($day ? "1" : "0"),
                                        'open_close_day' => $i,
                                        'user_id' => $id
                                    );
                                } else {
                                    $adata = array(
                                        'open_time_from' => '09:00:00',
                                        'close_time_to' => '21:00:00',
                                        'open_close_status' => ($day ? "1" : "0"),
                                        'open_close_day' => $i,
                                        'user_id' => $id
                                    );
                                }
                            } else {
                                $adata = array(
                                    'open_time_from' => '',
                                    'close_time_to' => '',
                                    'open_close_status' => ($day ? "1" : "0"),
                                    'open_close_day' => $i,
                                    'user_id' => $id
                                );
                            }
                            $res = $this->restaurantmodel->insert_restaurant_time($adata);
                        }

                        $res = $this->restaurantmodel->update_cuisine_ambience_data($id, $cuisine, $ambience);
                        $res1 = $this->restaurantmodel->update_dietary($id, $dietary);
                        $this->session->set_userdata('toast_message', 'Record updated successfully.');
                        $this->session->set_userdata('uploaded_img', $file_name);
                        $this->session->set_userdata('uploaded_img_hero', $hero_image);
                        $this->session->set_userdata('redirect_to', 'admin/restaurant_hero_crop');
                        redirect('admin/restaurant_profile_crop');

                    } else if (!empty($file_name)) {
                        $update_data['user_image'] = $file_name;
                        $result = $this->restaurantmodel->action('update', $update_data, $edit_id);
                        $id = $result;

                        $res = $this->restaurantmodel->update_restaurant_time1($id);
                        for ($i = 1; $i <= 7; $i++) {
                            $day = $this->input->post('day_' . $i);
                            $open_time = $this->input->post('open_from_' . $i);
                            $close_time = $this->input->post('closed_to_' . $i);
                            if (!empty($day)) {
                                if (!empty($open_time) && !empty($close_time)) {

                                    $adata = array(
                                        'open_time_from' => $open_time,
                                        'close_time_to' => $close_time,
                                        'open_close_status' => ($day ? "1" : "0"),
                                        'open_close_day' => $i,
                                        'user_id' => $id
                                    );
                                } else {
                                    $adata = array(
                                        'open_time_from' => '09:00:00',
                                        'close_time_to' => '21:00:00',
                                        'open_close_status' => ($day ? "1" : "0"),
                                        'open_close_day' => $i,
                                        'user_id' => $id
                                    );
                                }
                            } else {
                                $adata = array(
                                    'open_time_from' => '',
                                    'close_time_to' => '',
                                    'open_close_status' => ($day ? "1" : "0"),
                                    'open_close_day' => $i,
                                    'user_id' => $id
                                );
                            }
                            $res = $this->restaurantmodel->insert_restaurant_time($adata);
                        }
                        $res = $this->restaurantmodel->update_cuisine_ambience_data($id, $cuisine, $ambience);
                        $res1 = $this->restaurantmodel->update_dietary($id, $dietary);
                        $this->session->set_userdata('toast_message', 'Record updated successfully.');
                        $this->session->set_userdata('uploaded_img', $file_name);
                        $this->session->set_userdata('redirect_to', 'admin/restaurant');
                        redirect('admin/restaurant_profile_crop');
                    } else if (!empty($hero_image)) {
                        $update_data['restaurant_hero_image'] = $hero_image;
                        $result = $this->restaurantmodel->action('update', $update_data, $edit_id);
                        $id = $result;

                        $res = $this->restaurantmodel->update_restaurant_time1($id);
                        for ($i = 1; $i <= 7; $i++) {
                            $day = $this->input->post('day_' . $i);
                            $open_time = $this->input->post('open_from_' . $i);
                            $close_time = $this->input->post('closed_to_' . $i);

                            if (!empty($day)) {
                                if (!empty($open_time) && !empty($close_time)) {

                                    $adata = array(
                                        'open_time_from' => $open_time,
                                        'close_time_to' => $close_time,
                                        'open_close_status' => ($day ? "1" : "0"),
                                        'open_close_day' => $i,
                                        'user_id' => $id
                                    );
                                } else {
                                    $adata = array(
                                        'open_time_from' => '09:00:00',
                                        'close_time_to' => '21:00:00',
                                        'open_close_status' => ($day ? "1" : "0"),
                                        'open_close_day' => $i,
                                        'user_id' => $id
                                    );
                                }
                            } else {
                                $adata = array(
                                    'open_time_from' => '',
                                    'close_time_to' => '',
                                    'open_close_status' => ($day ? "1" : "0"),
                                    'open_close_day' => $i,
                                    'user_id' => $id
                                );
                            }
                            $res = $this->restaurantmodel->insert_restaurant_time($adata);
                        }

                        $res = $this->restaurantmodel->update_cuisine_ambience_data($id, $cuisine, $ambience);
                        $res1 = $this->restaurantmodel->update_dietary($id, $dietary);
                        $this->session->set_userdata('toast_message', 'Record updated successfully.');
                        $this->session->set_userdata('uploaded_img_hero', $hero_image);
                        $this->session->set_userdata('redirect_to', 'admin/restaurant');
                        redirect('admin/restaurant_hero_crop');
                    } else {
                        $result = $this->restaurantmodel->action('update', $update_data, $edit_id);
                        $id = $result;

                        $res = $this->restaurantmodel->update_restaurant_time1($id);
                        for ($i = 1; $i <= 7; $i++) {
                            $day = $this->input->post('day_' . $i);
                            $open_time = $this->input->post('open_from_' . $i);
                            $close_time = $this->input->post('closed_to_' . $i);
                            if (!empty($day)) {
                                if (!empty($open_time) && !empty($close_time)) {

                                    $adata = array(
                                        'open_time_from' => $open_time,
                                        'close_time_to' => $close_time,
                                        'open_close_status' => ($day ? "1" : "0"),
                                        'open_close_day' => $i,
                                        'user_id' => $id
                                    );
                                } else {
                                    $adata = array(
                                        'open_time_from' => '09:00:00',
                                        'close_time_to' => '21:00:00',
                                        'open_close_status' => ($day ? "1" : "0"),
                                        'open_close_day' => $i,
                                        'user_id' => $id
                                    );
                                }
                            } else {
                                $adata = array(
                                    'open_time_from' => '',
                                    'close_time_to' => '',
                                    'open_close_status' => ($day ? "1" : "0"),
                                    'open_close_day' => $i,
                                    'user_id' => $id
                                );
                            }
                            $res = $this->restaurantmodel->insert_restaurant_time($adata);
                        }

                        $res = $this->restaurantmodel->update_cuisine_ambience_data($id, $cuisine, $ambience);
                        $res1 = $this->restaurantmodel->update_dietary($id, $dietary);
                        $this->session->set_userdata('toast_message', 'Record updated successfully.');
                        redirect('admin/restaurant');
                    }
                } else {
                    //insert record
                    $password = hash('SHA256', $_POST['new_password']);
                    $cuisine = $this->input->post('cusine');
                    $ambience = $this->input->post('ambience');
                    $dietary = $this->input->post('dietary');

                    $restaurant_details_url_string = preg_replace('/[^A-Za-z0-9\-]/', '-', $this->input->post('txtrname'));
                    $restaurant_details_url_string = preg_replace('/-+/', '-', $restaurant_details_url_string);
                    $restaurant_details_url_string = strtolower($restaurant_details_url_string);
                    $count = $this->usermodel->restaurant_details_same_url_count($restaurant_details_url_string);
                    if ($count) {
                        $restaurant_details_url_string = $restaurant_details_url_string . '-' . $count;
                    }



                    $insert_data = array(
                        'user_password' => $password,
                        'user_image' => $file_name,
                        'restaurant_hero_image' => $hero_image,
                        'user_first_name' => $this->input->post('txtrname'),
                        'restaurant_owner_name' => $this->input->post('resownername'),
                        'user_email' => $this->input->post('txtemail'),
                        'user_description' => $this->input->post('description'),
                        'user_contact' => $this->input->post('txtcontact'),
                        'average_spend' => $this->input->post('avgspend'),
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'city_id' => $this->input->post('city_id'),
                        'country_id' => 47,
                        'region_id' => $this->input->post('region_id'),
                        'street_address1' => $this->input->post('address'),
                        'user_type' => '3',
                        'user_status' => '1',
                        'role_id' => '3',
                        'web_domain' => trim($this->input->post('txtrwebdomain')),
                        'restaurant_detail_url' => $restaurant_details_url_string
                    );
                    $result = $this->restaurantmodel->action('insert', $insert_data);
                    $id = $result;

                    for ($i = 1; $i <= 7; $i++) {
                        $day = $this->input->post('day_' . $i);
                        $open_time = $this->input->post('open_from_' . $i);
                        $close_time = $this->input->post('closed_to_' . $i);
                        if (!empty($day)) {
                            if (!empty($open_time) && !empty($close_time)) {

                                $adata = array(
                                    'open_time_from' => $open_time,
                                    'close_time_to' => $close_time,
                                    'open_close_status' => ($day ? "1" : "0"),
                                    'open_close_day' => $i,
                                    'user_id' => $id
                                );
                            } else {
                                $adata = array(
                                    'open_time_from' => '09:00:00',
                                    'close_time_to' => '21:00:00',
                                    'open_close_status' => ($day ? "1" : "0"),
                                    'open_close_day' => $i,
                                    'user_id' => $id
                                );
                            }
                        } else {
                            $adata = array(
                                'open_time_from' => '',
                                'close_time_to' => '',
                                'open_close_status' => ($day ? "1" : "0"),
                                'open_close_day' => $i,
                                'user_id' => $id
                            );
                        }
                        $res = $this->restaurantmodel->insert_restaurant_time($adata);
                    }

                    $res = $this->restaurantmodel->update_cuisine_ambience_data($id, $cuisine, $ambience);
                    $res1 = $this->restaurantmodel->update_dietary($id, $dietary);
                    if ($result) {
                        $this->session->set_userdata('toast_message', 'Record added successfully.');

                        $this->session->set_userdata('uploaded_img', $file_name);
                        $this->session->set_userdata('uploaded_img_hero', $hero_image);
                        $this->session->set_userdata('redirect_to', 'admin/restaurant_hero_crop');
                        redirect('admin/restaurant_profile_crop');

                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record.');
                    }
                }
            } else {
                $this->session->set_userdata('toast_error_message', 'All fields are mandatory.');
                redirect('admin/restaurant/addedit/' . $edit_id, 'refresh');
            }
        }

    }


    /*
     * Method Name: get_closing_slots
     * Purpose: To get slots greater than inputed slots;
     * params:
     *      input: - slot_id
     *      output: - array() - time slots
     */
    public function get_closing_slots()
    {
        $from_time_slot = $this->input->post('slot_id');
        $closing_time_slots = $this->restaurantmodel->get_closing_slots($from_time_slot);
        if($closing_time_slots)
        {
            echo json_encode(array('success' => 1, 'closing_time_slots' => $closing_time_slots));
        }
        else
        {
            echo json_encode(array('success' => 0));
        }

    }

    //Function to delete members
    public function delete_members($user_id = 0)
    {
        $update_array = array(
            'is_deleted' => '1'
        );
        $this->restaurantmodel->action('update', $update_array, $user_id);
        $this->session->set_userdata('toast_message', 'Record deleted successfully.');
        redirect('admin/restaurant', 'refresh');
    }

    public function upload_members_image()
    {
        if (!file_exists(MEMBER_IMAGE_PATH)) {
            mkdir(MEMBER_IMAGE_PATH, 0700, true);
        }
        $file_name = stripJunk($_FILES['image']['name']); //preg_replace('/[^a-zA-Z0-9_.]/s', '', $_FILES['image']['name']);

        $config = array(
            'upload_path' => MEMBER_IMAGE_PATH,
            'allowed_types' => "jpg|png|jpeg",
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

    //Function to upload the restaurant hero image
    public function upload_restaurant_hero_image()
    {
        if (!file_exists(MEMBER_IMAGE_PATH)) {
            mkdir(MEMBER_IMAGE_PATH, 0700, true);
        }
        $hero_image = stripJunk($_FILES['h_image']['name']); //preg_replace('/[^a-zA-Z0-9_.]/s', '', $_FILES['image']['name']);

        $config = array(
            'upload_path' => MEMBER_IMAGE_PATH,
            'allowed_types' => "jpg|png|jpeg",
            'overwrite' => FALSE,
            'max_size' => MAX_UPOAD_IMAGE_SIZE,
            'max_height' => "2160",
            'max_width' => "4096",
            'file_name' => $hero_image
        );
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('h_image')) {
            $upload_data = $this->upload->data();
            $data = array('file_name' => $upload_data['file_name']);
            return $data;
        } else {
            $error = array('error' => $this->upload->display_errors());
            return $error;
        }
    }

    //Function for updating the member status
    public function update_members_status()
    {
        $this->load->model('restaurantmodel', '', TRUE);
        $user_id = $this->input->post('user_id');
        $changeStatus = $this->input->post('changeStatus');
        if ($changeStatus)
            $changeStatus = '0';
        else
            $changeStatus = '1';
        $update_array = array(
            'user_status' => $changeStatus
        );
        $this->restaurantmodel->action('update', $update_array, $user_id);
        return 1;
    }

    // purpose : to add ads in addmst
    public function insert_add()
    {
        $user_id = $this->input->post('user_id');
        $explode = explode("add_", $user_id);
        $id = $explode[1];
        $this->restaurantmodel->insert_add($id);
        return 1;
    }

    // purpose : to add delete from addmst
    public function delete_add()
    {
        $user_id = $this->input->post('user_id');
        $explode = explode("add_", $user_id);
        $id = $explode[1];
        $this->restaurantmodel->delete_add($id);
        return 1;
    }

    public function toggle_top_10()
    {
        $user_id = $this->input->post('user_id') ? $this->input->post('user_id') : "";
        $flag = $this->input->post('flag') ? $this->input->post('flag') : 0;

        if ($user_id != "") {
            if ($flag == 0) {
                // Code to unmark
                $explode = explode("top10_", $user_id);
                $id = $explode[1];
                $this->restaurantmodel->delete_top10($id);
                echo 1;
            } else if ($flag == 1) {
                $cnt = $this->restaurantmodel->count_top10();
                // Code to mark

                if ($cnt < 10) {
                    $explode = explode("top10_", $user_id);
                    $id = $explode[1];
                    $this->restaurantmodel->insert_top10($id);
                    echo 1;
                } else
                    echo 0;
            }
        }
    }

    //Function for checking the existence of username
    public function check_username_exists($user_id = 0)
    {

        $username = $this->input->post("title");
        $this->db->select("user_id");
        $this->db->where("is_deleted", '0');
        $this->db->where("user_username", $username);
        if ($user_id)
            $this->db->where("user_id <>", $user_id);
        $result = $this->db->get("usermst");

        if ($result->num_rows() > 0) {
            echo "false";
        } else {
            echo "true";
        }
    }

    //Function for checking the email address
    public function check_email_exist($user_id = 0)
    {

        $email = $this->input->post("title");
        $this->db->select("user_id");
        $this->db->where("is_deleted", '0');
        $this->db->where("user_email", $email);
        if ($user_id)
            $this->db->where("user_id <>", $user_id);
        $result = $this->db->get("usermst");

        if ($result->num_rows() > 0) {
            echo "false";
        } else {
            echo "true";
        }
    }

}

?>