<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH . 'controllers/admin/Access.php';

/*
  Class that contains function for restaurant booking management
 */

class Booking extends Access
{

    // Constructor
    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/bookingmodel1', 'bookingmodel', TRUE);
        $this->load->model('admin/restaurant_table_model', 'restauranttablemodel', TRUE);
        $this->load->model('webservices/usermodel', 'usermodel', TRUE);
        $this->load->model('webservices/restaurantmodel', 'restaurantmodel', TRUE);
        $this->load->model('webservices/bookingmodel', 'wsbookingmodel', TRUE);
//        $this->load->model('admin/restaurant_table_model', 'restauranttablemodel', TRUE);
    }

    /*
     * Method Name: index
     * Purpose: To load all the time slots and tables of the restaurant.
     */

    public function index()
    {
        $restaurant_id = $this->session->userdata('user_id');

        $date = date('d-m-Y');
        $data['date_selected'] = $date;

        $restaurant_tables = $this->bookingmodel->get_table_details($restaurant_id, $date);
//            var_dump($restaurant_tables);die;

        $time_slots = $tables = $booked_table = $time_slot_arr = $table_arr = $booked_ids = array();
        $total_capacity = 0;

        if ($restaurant_tables) {
            foreach ($restaurant_tables as $table) {
                if (!in_array($table->time_slot, $time_slot_arr)) {
                    array_push($time_slot_arr, $table->time_slot);
                    $time_slots[] = array('time_slot' => $table->time_slot, 'slot_id' => $table->slot_id);
                }
                if (!in_array($table->table_name, $table_arr)) {
                    array_push($table_arr, $table->table_name);
                    $tables[] = array('table_name' => $table->table_name, 'table_capacity' => $table->table_capacity, 'table_id' => $table->table_id);
                    $total_capacity = $total_capacity + $table->table_capacity;
                }

                $booked_table[$table->table_name . "-" . $table->time_slot] = $table->is_booked;
                $booked_ids[$table->table_name . "-" . $table->time_slot . "-id"] = $table->booking_id;
            }
        }

        $data['total_capacity'] = $total_capacity;
        $data['time_slots'] = $time_slots;
        $data['restaurant_tables'] = $tables;
        $data['booked_tables'] = $booked_table;
        $data['booked_ids'] = $booked_ids;
        $this->template->view('booking_grid', $data);
    }

    /*
     * Method Name: reserve_table
     * Purpose: To reserve table of the restaurant.
     * params:
     *      input: -
     */

    public function reserve_table()
    {
        $booking_is_reserved = $this->input->post('booking_is_reserved');
        $time = $this->input->post('booking_time_slot');
        $date = $this->input->post('booking_table_date');
        $booking_table = $this->input->post('booking_table');
        $booking_code = $this->input->post('booking_code');

        // If reserved we need to release
        if ($booking_is_reserved == 1) {
            $delete_array = array(
                'restaurant_id' => $this->session->userdata('user_id'),
                'request_date' => date('Y-m-d H:i:s', strtotime($date)),
                'table_id' => $booking_table,
                'time_slot_id' => $time
            );

            $this->bookingmodel->action('admin_booking_request', 'delete', $delete_array);

            echo json_encode(array('success' => 1, 'message' => 'Table released.'));
        } else {
            $is_walkin_user = 1;
            $booking_id = 0;

            // Get booking id if code exists
            if ($booking_code != '') {
                $is_walkin_user = 0;
                $booking_id = $this->bookingmodel->get_booking_id($booking_code);
            }

            // Insert record in database
            $insert_array = array(
                'restaurant_id' => $this->session->userdata('user_id'),
                'request_date' => date('Y-m-d H:i:s', strtotime($date)),
                'table_id' => $booking_table,
                'booking_id' => $booking_id,
                'time_slot_id' => $time,
                'walkin_user' => $is_walkin_user,
                'created_on' => date('Y-m-d H:i:s')
            );
            $insert_result = $this->bookingmodel->action('admin_booking_request', 'insert', $insert_array);

            if ($insert_result > 0)
                echo json_encode(array('success' => 1, 'message' => 'Table reserved successfully.'));
            else
                echo json_encode(array('success' => 0, 'message' => 'Table reservation failed.'));
        }
    }

    /*
     * Method Name: load_booking_table
     * Purpose: To load all the time slots and tables of the restaurant for particular date.
     */

    public function load_booking_table()
    {
        $restaurant_id = $this->session->userdata('user_id');

        $date = $this->input->post('date');
        $data['date_selected'] = $date;

        $restaurant_tables = $this->bookingmodel->get_table_details($restaurant_id, $date);
        $time_slots = $tables = $booked_table = $time_slot_arr = $table_arr = $booked_ids = array();
        $total_capacity = 0;

        if ($restaurant_tables) {
            foreach ($restaurant_tables as $table) {
                if (!in_array($table->time_slot, $time_slot_arr)) {
                    array_push($time_slot_arr, $table->time_slot);
                    $time_slots[] = array('time_slot' => $table->time_slot, 'slot_id' => $table->slot_id);
                }
                if (!in_array($table->table_name, $table_arr)) {
                    array_push($table_arr, $table->table_name);
                    $tables[] = array('table_name' => $table->table_name, 'table_capacity' => $table->table_capacity, 'table_id' => $table->table_id);
                    $total_capacity = $total_capacity + $table->table_capacity;
                }

                $booked_table[$table->table_name . "-" . $table->time_slot] = $table->is_booked;
                $booked_ids[$table->table_name . "-" . $table->time_slot . "-id"] = $table->booking_id;
            }
        }

        $data['total_capacity'] = $total_capacity;
        $data['time_slots'] = $time_slots;
        $data['restaurant_tables'] = $tables;
        $data['booked_tables'] = $booked_table;
        $data['booked_ids'] = $booked_ids;

        if (empty($time_slots)) {
            echo json_encode(array('success' => 0, 'message' => 'Restaurant is closed for selected the day.', 'view' => $this->load->view('admin/booking_time_table_view', $data, TRUE)));
        } else {
            echo json_encode(array('success' => 1, 'message' => 'Bookings loaded successfully.', 'view' => $this->load->view('admin/booking_time_table_view', $data, TRUE)));
        }

        // } else {
        //     echo json_encode(array('success' => 0, 'message' => 'Cant fetch bookings.'));
    }


    /*
     * Method Name: get_booking_number
     * Purpose: To get booking number using booking id.
     * params:
     *      input: - booking_id
     *      output: - booking_number
     */
    public function get_booking_number()
    {
        $booking_id = $this->input->post('booking_id');
        $booking_number = $this->bookingmodel->get_booking_number($booking_id);
        if ($booking_number) {
            echo json_encode(array('success' => 1, 'booking_number' => $booking_number));
        } else {
            echo json_encode(array('success' => 0, 'booking_number' => "Booking number not found"));
        }

    }

    /*
     * Method Name: booking_list
     * Purpose: Redirect to booking list with current date data.
     * params:
     *      output: - booking list data
     */
    public function booking_list()
    {
        $this->template->view('booking_list');
    }

    /*
     * Method Name: get_booking_list
     * Purpose: Get bookings of inputed date and return list
     * params:
     *      input: date
     *      output: - booking list data for that date
     */
    public function get_booking_list()
    {
        $selected_date = $this->input->post('date');
        $restaurant_id = $this->session->userdata('user_id');

        $search_string = $this->session->userdata('slider_search');

        //---- DataTable Draw -----//
        $iDraw = $this->input->post('draw');

        //---- DataTable Length: Number of record on one page -----//
        $iLength = $this->input->post('length');

        //---- DataTable Start: Start record from -----//
        $iRecordStartFrom = $this->input->post('start');

        $iPageSize = $iLength; #=== ONE PAGE RECORDS


        ##======== SEARCH CONDITION ========##
        $aPostArray = $this->input->post(); /*         * *** POST ARRAY **** */

        $sSearchUserNameString = $aPostArray['columns'][2]['search']['value']; /*         * *** SEARCH BY USER NAME **** */


        $first_date = date('Y-m-d H:i:s', strtotime($selected_date));
        $next_date = date('Y-m-d H:i:s', strtotime($selected_date . '+1 day'));
        $booking_data = $this->bookingmodel->get_booking_list($restaurant_id, $first_date, $next_date, $sSearchUserNameString);
        $iOrderCount = count($booking_data);
        if ($booking_data) {
            foreach ($booking_data as $key => $data) {
                $aDataTableResponce[$key] = array();
                $booking_by = "";
                $edit_link = "";
                $table_name_string = "";
                $table_name_array = array();
                $table_details = $this->bookingmodel->get_booked_table_details_using_booking_id($restaurant_id, $data['booking_id']);
                if($table_details){
                    foreach ($table_details as $table_detail) {
                        $table_name_array[] = $table_detail['table_name'];
                    }
                }
                $table_name_string = implode(", \n",$table_name_array);
                $booking_data[$key]['table_name'] = $table_details;

                $aDataTableResponce[$key] = array();
                if ($data['booking_by'] == 1) {
                    $booking_by = "<i class='fa fa-mobile' aria-hidden='true'></i>";
                } else if ($data['booking_by'] == 2) {
                    $booking_by = "<i class='fa fa-globe' aria-hidden='true'></i>";
                } else if ($data['booking_by'] == 3) {
                    $booking_by = "<i class='fa fa-phone' aria-hidden='true'></i>";
                }

                $edit_link = "<a href='". $this->config->item('admin_url') ."booking/addedit/". $data['booking_id'] . "' ><i class='fa fa-pencil-square-o fa-2x' ></i></a>";
                $table_list_link = "<a href='#' class='tdHover' title='Table Name(s)' data-content='". $table_name_string . "'> <i class='fa fa-th-list' aria-hidden='true'></i>  </a>";

                array_push($aDataTableResponce[$key], $key + 1 + $iRecordStartFrom);
                array_push($aDataTableResponce[$key], $data['booking_time']);
                array_push($aDataTableResponce[$key], $data['user_name']);
                array_push($aDataTableResponce[$key], $data['number_of_guest']);
                array_push($aDataTableResponce[$key], $table_list_link);
                array_push($aDataTableResponce[$key], $booking_by);
                array_push($aDataTableResponce[$key], $edit_link);
            }

            echo json_encode(array('success' => SUCCESS, 'result' => $booking_data, "date" => $first_date, "recordsTotal" => $iOrderCount, "recordsFiltered" => $iOrderCount, "data" => $aDataTableResponce, "search_string" => $sSearchUserNameString));
        } else {
            echo json_encode(array('success' => FAIL, 'message' => NO_BOOKING, "date" => $first_date, "recordsTotal" => $iOrderCount, "recordsFiltered" => $iOrderCount, "data" => ''));
        }
    }

    /*
     * Method Name: addedit
     * Purpose: Booking add edit
     * params:
     *      input:
     *      output: - Success
     */
    public function addedit($edit_id = 0)
    {
        $restaurant_id = $this->session->userdata('user_id');
        $is_table_available = $this->restauranttablemodel->get_tables($restaurant_id);
        if ($is_table_available) {
            $data = array();
            $data['edit_id'] = $edit_id;
            $formData = array(
                'no_of_guest' => '',
                'booking_date' => '',
                'time_slots' => '',
                'booking_table_list' => '',
            );

            if (empty($_POST)) {
                if ($edit_id) {
                    $editData = $this->bookingmodel->getData($edit_id);
                    if ($editData) {
                        //Get available time slots for booking date and check for status for which is selected time slot
                        $date = date('Y-m-d H:i:s', strtotime($editData->booking_date));
                        $time_slots = $this->restaurantmodel->get_available_time_slots_for_restaurant($date, $restaurant_id);
                        $time_slots = $time_slots['time_slots'];
                        $flag = 0;
                        foreach ($time_slots as $key => $time_slot) {
                            if ($time_slot['time_slot'] == $editData->booking_time) {
                                $time_slots[$key]['status'] = SUCCESS;
                                $flag = 1;
                            } else {
                                $time_slots[$key]['status'] = FAIL;
                            }
                        }
                        if ($flag != 1) {
                            $insert_index = sizeof($time_slots);
                            $time_slots[$insert_index]['slot_id'] = $editData->time_slot_id;
                            $time_slots[$insert_index]['time_slot'] = $editData->booking_time;
                            $time_slots[$insert_index]['status'] = SUCCESS;
                        }

                        //Get available table list and check for status which is selected

                        $date_time = date('Y-m-d H:i:s', strtotime("$editData->booking_date $editData->booking_time"));
                        $table_list = $this->restaurantmodel->get_tables($restaurant_id, $date, $editData->time_slot_id, $editData->booking_time, $date_time);
                        $booking_table_list = array();
                        $booked_table_ids = explode(',', $editData->table_ids);
                        foreach ($booked_table_ids as $booked_table_id) {
                            if($booked_table_id > 0) {
                                $table_data = $this->restauranttablemodel->get_table_data($booked_table_id);
                                $temp_table = array();
                                $temp_table['status'] = SUCCESS;
                                $temp_table['table_id'] = $table_data->table_id;
                                $temp_table['table_name'] = $table_data->table_name;
                                $temp_table['table_capacity'] = $table_data->table_capacity;
                                $booking_table_list[] = $temp_table;
                            }
                        }


                        if ($table_list['is_table_list'] == 1) {
                            $table_list = $table_list['response']['table_list'];
                            foreach ($table_list as $key => $item) {
                                $temp_table = array();
                                $temp_table['status'] = FAIL;
                                $temp_table['table_id'] = $item['table_id'];
                                $temp_table['table_name'] = $item['table_name'];
                                $temp_table['table_capacity'] = $item['table_capacity'];
                                $booking_table_list[] = $temp_table;
                            }
                        }

                        $formData = array(
                            'no_of_guest' => $editData->number_of_guest,
                            'booking_date' => $editData->booking_date,
                            'time_slots' => $time_slots,
                            'booking_table_list' => $booking_table_list
                        );
                    }
                }
                $data['formData'] = $formData;
                $this->template->view('addbooking', $data);
            } else {

                if (!empty($_POST['booking_date']) && !empty($_POST['booking_time'])) {

                    $no_of_guest = $this->input->post('no_of_guest');
                    $booking_date = $this->input->post('booking_date');
                    $booking_time = $this->input->post('booking_time');
                    $table_ids = $this->input->post('booking_table');
                    $booking_by = "3";
					
					
                    $time_slot = $this->wsbookingmodel->get_time_slot($booking_time);
                    $time_slot = json_decode(json_encode($time_slot), true);
                    $time_slot = $time_slot[0]['time_slot'];
                    $from_time = date('Y-m-d H:i:s', strtotime("$booking_date $time_slot"));
                    $to_time = date("Y-m-d H:i:s", strtotime("$booking_date $time_slot +30 minutes"));
                    $date = date('Y-m-d H:i:s', strtotime($booking_date));

                    $edit_id = $this->input->post('edit_id');
                    if ($edit_id) {
                        $update_last_minute_data = array();
                        $has_requested = $this->wsbookingmodel->has_requested_table($restaurant_id, $date, $booking_time, $table_ids, $edit_id);
                        if ($has_requested['status'] == 0) {
                            $this->session->set_userdata('toast_error_message', 'Sorry for inconvenience, The table you have selected is already booked.');
                            redirect('admin/booking/addedit/' . $edit_id, 'refresh');
                        }

                        $update_data = array(
                            'booking_from_time' => $from_time,
                            'booking_to_time' => $to_time,
                            'number_of_guest' => $no_of_guest,
                            'booking_status_change_on' => date("Y-m-d H:i:s"),
                        );

                        $update_status = $this->wsbookingmodel->update_booking_request($update_data, $restaurant_id, $table_ids, $date, $booking_time, $edit_id, $update_last_minute_data);
                        if ($update_status) {
                            $this->session->set_userdata('toast_message', 'Booking updated successfully.');
                            redirect('admin/booking/booking_list');
                        } else {
                            $this->session->set_userdata('toast_error_message', 'Sorry for inconvenience. Unable to update booking, Please try again.');
                            redirect('admin/booking/addedit/' . $edit_id, 'refresh');
                        }

                    } else {
                        //insert booking
                        $booking_number = rand_string(7);
                        $has_requested = $this->wsbookingmodel->has_requested_table($restaurant_id, $date, $booking_time, $table_ids);
                        if ($has_requested['status'] == 0) {
                            $this->session->set_userdata('toast_error_message', 'Sorry for inconvenience. The table you have selected is already booked.');
                            redirect('admin/booking/addedit', 'refresh');
                        }
                        $insert_last_minute_data = array();


                        $is_user_details = $this->input->post('is_user_fields');
                        if (!$is_user_details) {
                            $user_id = $this->input->post('user_id');
                        } else {
                            $user_type = SEARCH_APP_USER_TYPE;
//                            $first_name = $this->input->post("user_name") ? $this->input->post("user_name") : "";
//                            $email = $this->input->post("user_email") ? $this->input->post("user_email") : "";
//                            $date_of_birth = $this->input->post("date_of_birth") ? $this->input->post("date_of_birth") : "";
//                            $contact_number = $this->input->post("txtcontact") ? $this->input->post("txtcontact") : "";
//                            $country = $this->input->post('country_id');
//                            $gender = $this->input->post("gender") ? $this->input->post("gender") : "";
//                            $state = $this->input->post("region_id") ? $this->input->post("region_id") : "";
//                            $city = $this->input->post("city_id") ? $this->input->post("city_id") : "";
//                            $is_subscribe = 0;
                            $first_name = $this->input->post("user_name");
                            $email = $this->input->post("user_email");
                            $date_of_birth = $this->input->post("date_of_birth");
                            $contact_number = $this->input->post("txtcontact");
                            $country = 47;
                            $gender = $this->input->post("gender");
                            $state = $this->input->post("region_id");
                            $city = $this->input->post("city_id");
                            $is_subscribe = 0;

//                            if (empty($first_name) || empty($email) || empty($date_of_birth) || empty($contact_number) || ($country <= 0) || ($state <= 0) || ($city <= 0) || empty($gender)) {
//                                $this->session->set_userdata('toast_error_message', 'All fields are mandatory.');
//                                redirect('admin/booking/addedit/', 'refresh');
//                            }

                            $user_insert_data = array(
                                'user_first_name' => $first_name,
                                'user_email' => $email,
                                'date_of_birth' => date('Y-m-d', strtotime($date_of_birth)),
                                'country_id' => $country,
                                'region_id' => $state,
                                'city_id' => $city,
                                'user_contact' => $contact_number,
                                'user_type' => $user_type,
                                'gender' => $gender,
                                'user_status' => '1',
                                'is_deleted' => '0',
                                'notification_setting' => '0',
                                'created_on' => date("Y-m-d H:i:s")
                            );

                            $user_id = $this->usermodel->action('insert', $user_insert_data);
                        }

                        $insert_data = array(
                            'customer_id' => $user_id,
                            'restaurant_id' => $restaurant_id,
                            'booking_number' => $booking_number,
                            'booking_from_time' => $from_time,
                            'booking_to_time' => $to_time,
                            'number_of_guest' => $no_of_guest,
                            'status' => 3,
                            'payment_status' => 0,
                            'booking_on' => date("Y-m-d H:i:s"),
                            'booking_status_change_on' => date("Y-m-d H:i:s"),
                            'booking_by' => $booking_by
                        );

                        $insert_result = $this->wsbookingmodel->insert_booking_request($insert_data, $table_ids, $date, $booking_time, $insert_last_minute_data);
                        if ($insert_result['status'] == 1) {
                            $this->session->set_userdata('toast_message', 'Booking created successfully.');
                            redirect('admin/booking/booking_list');
                        } else {
                            $this->session->set_userdata('toast_error_message', 'Sorry for inconvenience. Unable to add booking, Please try again.');
                            redirect('admin/booking/addedit/', 'refresh');
                        }
                    }
                } else {
                    $this->session->set_userdata('toast_error_message', 'All fields are mandatory.');
                    redirect('admin/booking/addedit/' . $edit_id, 'refresh');
                }

            }
        } else {
            $this->session->set_userdata('toast_error_message', 'Table not added by you.');
            redirect('admin/booking/booking_list', 'refresh');
        }
    }

    /*
     * Method Name: get_time_slot
     * Purpose: Get time slot which are available for selected date
     * params:
     *      input: date
     *      output: - booking list data for that date
     */
    public function get_time_slot()
    {
        $restaurant_id = $this->session->userdata('user_id');
        $date = $this->input->post('date');
        $date = date('Y-m-d H:i:s', strtotime($date));
        $time_slots = $this->restaurantmodel->get_available_time_slots_for_restaurant($date, $restaurant_id);
        if ($time_slots) {
            $time_slots = $time_slots['time_slots'];
            echo json_encode(array('success' => SUCCESS, 'time_slots' => $time_slots));
        } else {
            echo json_encode(array('success' => FAIL, 'message' => NO_TIME_SLOT_TODAY));
        }
    }

    /*
    * Method Name: get_table_list
    * Purpose: Get table list or next four available time slots
    * params:
    *      input: time slot id and date
    *      output: - booking list data for that date
    */
    public function get_table_list()
    {
        $restaurant_id = $this->session->userdata('user_id');
        $date = $this->input->post('date');
        $slot_id = $this->input->post('slot_id');

        $start_time_slot = $this->wsbookingmodel->get_time_slot($slot_id);
        $start_time_slot = json_decode(json_encode($start_time_slot), true);
        $start_time_slot = $start_time_slot[0]['time_slot'];
        $date_time = date('Y-m-d H:i:s', strtotime("$date $start_time_slot"));
        $date = date('Y-m-d H:i:s', strtotime("$date"));
        $result_array = $this->restaurantmodel->get_tables($restaurant_id, $date, $slot_id, $start_time_slot, $date_time);
        echo json_encode(array('success' => SUCCESS, 'result' => $result_array));
    }

    public function check_for_user_exist()
    {
        $email = $this->input->post('email');
        $user_details = $this->usermodel->get_user_details_for_password($email);
        if ($user_details) {
            echo json_encode(array('success' => SUCCESS, 'result' => $user_details));
        } else {
            echo json_encode(array('success' => FAIL, 'result' => array()));
        }

    }
}


?>