<?php

/*
 * Programmer Name: Preeti M
 * Purpose:Access Controller
 * Date:06-10-2016
 * Dependency: accessmodel.php
 */

class Access extends CI_Controller
{
  function __construct()
  {
    parent::__construct();

    $this->load->model('admin/accessmodel', 'accessmodel', TRUE);

    if( $this->session->userdata('user_id') )
    {
      $option_name = $this->uri->segment( 2 );
      $option_id = $this->accessmodel->get_option_id( $option_name );

      if( $this->session->userdata('user_type') == SEARCH_ADMIN_TYPE )
      {
        if( in_array( $option_id, $this->config->item('restaurant_admin_menu') ) )
          redirect('admin');
      }
      else if( $this->session->userdata('user_type') == SEARCH_RESTAURANT_TYPE )
      {
        if( !in_array( $option_id, $this->config->item('restaurant_admin_menu') ) && $option_id != 29 && $option_id != 30 )
          redirect('admin');
      }
      else if( $this->session->userdata('user_type') == SEARCH_SUBADMIN_TYPE )
      {
        $has_access = $this->accessmodel->check_user_access( $this->session->userdata('user_id'), $option_id );
        if( !$has_access )
          redirect('admin');
      }
    }
    else
      redirect('admin');
  }
}

?>