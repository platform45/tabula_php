<?php

/*
 * Programmer Name:Akash Deshmukh
 * Purpose:Admin Controller
 * Date:02 Sept 2016
 * Dependency: adminmodel.php
 */

class Calendar extends CI_Controller
{
    /*
     * Purpose: Constructor.
     * Date: 02 Sept 2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct()
    {
        parent::__construct();
        $this->load->model('admin/calendarmodel', 'calendarmodel', TRUE);
    }

    function index()
    {
        $this->template->view('calendar');
    }

    public function get_monthly_booking_daywise_count()
    {
        $restaurant_id = $this->session->userdata('user_id');
        $result = array();
        if ($restaurant_id) {
            $todayDate = $this->input->post('todayDate');
            $dt = new DateTime("@$todayDate");
            $dt->setTimeZone(new DateTimeZone(CLIENT_ZONE));
            $todayDate = $dt->format('Y-m-d H:i:s');

            $firstDay = date('Y-m-01 00:00:00', strtotime($todayDate));
            $lastDay = date('Y-m-t', strtotime($todayDate));
            $lastDay = date('Y-m-d 00:00:00', strtotime($lastDay . ' +1 day'));

            $booking_data = $this->calendarmodel->get_monthly_booking_daywise_count($restaurant_id, $firstDay, $lastDay);
            $data = array();

            $booking_data = json_decode(json_encode($booking_data), true);
            if ($booking_data) {
                foreach ($booking_data as $dt) {
                    $temp = array();
                    $temp['date'] = date('D M d Y 20:00:00 O', strtotime($dt['booking_date']));
                    $temp['count'] = $dt['count'];
                    $data[] = $temp;
                }
                $result['success'] = SUCCESS;
                $result['booking_data'] = $booking_data;
                $result['data'] = $data;
                echo json_encode($result);
            } else {
                $result['success'] = FAIL;
                echo json_encode($result);
            }
        } else {
            $result['success'] = FAIL;
            $result['message'] = NOT_LOG_IN;
            echo json_encode($result);
        }

    }
}


?>