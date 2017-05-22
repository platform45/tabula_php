<?php

/*
 * Programmer Name:SK
 * Purpose:Role Controller
 * Date:19 Dec 2014
 * Dependency: slidermodel.php
 */

class Unsubscribe extends CI_Controller {
    /*
     * Purpose: Constructor.
     * Date: 19 Dec 2014
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct() {
        parent::__construct();
        $this->load->model('admin/unsubscribemodel', '', TRUE);
    }

    /*
     * Purpose: To Load role
     * Date: 19 Dec 2014
     * Input Parameter: None
     * Output Parameter: None
     */

    public function newsletter($sub_id) {

        $email = $this->unsubscribemodel->getEmail($sub_id);
        if ($email) {
            $emailData = $this->unsubscribemodel->unsubscribe($email);
        }
    }

}

?>
	