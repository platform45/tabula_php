<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH.'controllers/admin/Access.php';

/*
 * Programmer Name: Akash Deshmukh
 * Purpose: Foodmenu Controller
 * Date: 02 Sept 2016
 * Dependency: foodmenumodel.php
 */

class Menucard_view extends Access {
    /*
     * Purpose: Constructor.
     * Date: 02 Sept 2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct() {
        parent::__construct();
        $this->load->model('admin/menucardmodel', 'menucardmodel', TRUE);
        $this->load->model('admin/restaurantmodel', 'restaurantmodel', TRUE);
    }

      public function menu_view($user_id) {
        $type="1";
        $data['sliderData'] = $this->menucardmodel->getData($user_id,"",$type);
        $this->template->view('menucard_view', $data);
    
}


}

?>