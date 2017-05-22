<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Page_not_found extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        $this->load->view("page_not_found");
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */