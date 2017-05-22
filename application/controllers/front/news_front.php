<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class News_front extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('webservices/usermodel', 'usermodel', TRUE);
        $this->load->model('webservices/restaurantmodel', 'restaurantmodel', TRUE);
        $this->load->model('webservices/contentmodel', 'contentmodel', TRUE);
        $this->load->model('webservices/newsmodel', 'newsmodel', TRUE);
		$this->load->model('webservices/notificationmodel', 'notificationmodel', TRUE );
    }

    public function index()
    {
		$this->session->unset_userdata('book_slot_id');
		$this->session->unset_userdata('modified_booking');
		
        $country_id = STATIC_COUNTRY_ID;
        $data['states'] = $this->usermodel->get_state_by_country($country_id);

        $data['news'] = $this->restaurantmodel->load_news();
        $this->template_front->view('newslisting', $data);
    }

    public function news_details()
    {
        $country_id = STATIC_COUNTRY_ID;
        $data['states'] = $this->usermodel->get_state_by_country($country_id);

        $news_description_link = $this->uri->segment(2);
        $data['news_details'] = $this->newsmodel->getNewsDetailsByDescriptionLink($news_description_link);
        $this->template_front->view('newsdetails', $data);
    }
}

?>