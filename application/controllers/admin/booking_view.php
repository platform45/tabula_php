<?php

/*
  Class that contains function for restaurant booking management
 */

class Booking_view extends CI_Controller {

    // Constructor
    function __construct() {
        parent::__construct();

        $this->load->model('admin/bookingmodel', '', TRUE);
        $this->load->model('admin/restaurant_table_model', '', TRUE);
    }

    /*
     * Method Name: index
     * Purpose: To load all the time slots and tables of the restaurant.
     */

    public function booking_list($user_id) {
        $restaurant_id = $user_id;

        if ($this->session->userdata('user_id')) {
            $date = date('d-m-Y');
            $data['date_selected'] = $date;
            $type = 1; // 1 for admim getting request for restaurant
            $restaurant_tables = $this->bookingmodel->get_table_details_user($restaurant_id, $date);

            $time_slots = $tables = $booked_table = $time_slot_arr = $table_arr = array();
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
                }
            }

            $data['total_capacity'] = $total_capacity;
            $data['time_slots'] = $time_slots;
            $data['restaurant_tables'] = $tables;
            $data['booked_tables'] = $booked_table;
            $data['user_id'] = $user_id;
            $this->template->view('booking_grid_view', $data);
        } else {
            redirect('admin');
        }
    }

    /*
     * Method Name: load_booking_table
     * Purpose: To load all the time slots and tables of the restaurant for particular date.
     */

    public function load_booking_table() {

        if ($this->session->userdata('user_id')) {
            $date = $this->input->post('date');
            $user_id = $this->input->post('user_id');
            $restaurant_id = $user_id;
            $data['date_selected'] = $date;

            $restaurant_tables = $this->bookingmodel->get_table_details_user($restaurant_id, $date);
            $time_slots = $tables = $booked_table = $time_slot_arr = $table_arr = array();
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
                }
            }

            $data['total_capacity'] = $total_capacity;
            $data['time_slots'] = $time_slots;
            $data['restaurant_tables'] = $tables;
            $data['booked_tables'] = $booked_table;
            echo json_encode(array('success' => 1, 'message' => 'Bookings loaded successfully.', 'view' => $this->load->view('admin/booking_time_table_view1', $data, TRUE)));
        } else {
            echo json_encode(array('success' => 0, 'message' => 'Cant fetch bookings.'));
        }
    }

}

?>