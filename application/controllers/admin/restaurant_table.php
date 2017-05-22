<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH.'controllers/admin/Access.php';

/*
  Class that contains function for restaurant table management
 */

class Restaurant_table extends Access {

  // Constructor
  function __construct()
  {
    parent::__construct();
    $this->load->model('admin/restaurant_table_model', '', TRUE);
  }

  /*
   * Method Name: index
   * Purpose: To load all the tables of the restaurant.
   */
  public function index()
  {
      $restaurant_id = $this->session->userdata('user_id');
      $data['restaurant_tables'] = $this->restaurant_table_model->get_tables( $restaurant_id );
      $this->template->view('restaurant_table', $data);
    
  }

  /*
   * Method Name: delete_table
   * Purpose: To delete table of the restaurant.
   * params:
   *      input: - table id
   */
  public function delete_table( $table_id = 0 )
  {
    $update_array = array( 'is_deleted' => '1' );
    $this->restaurant_table_model->action('update', $update_array, $table_id);
    $this->session->set_userdata('toast_message', 'Record deleted successfully.');
    redirect('admin/tables', 'refresh');
  }

  /*
   * Method Name: update_table_status
   * Purpose: To update status of table for the restaurant.
   * params:
   *      input: - table_id, status
   */
  public function update_table_status()
  {
    $table_id = $this->input->post('table_id');
    $change_status = $this->input->post('change_status');

    if ( $change_status )
      $change_status = '0';
    else
      $change_status = '1';

    $update_array = array( 'status' => $change_status );
    $this->restaurant_table_model->action('update', $update_array, $table_id);
    return 1;
  }

  /*
   * Method Name: addedit
   * Purpose: To edit table data for the restaurant.
   * params:
   *      input: - table_id for edit
   */
  public function addedit( $table_id = 0 )
  {
    
      $data = array();
      $data['table_id'] = $table_id;
      $form_data = array(
        'txt_table_name' => '',
        'txt_table_capacity' => ''
      );

      if( !empty( $_POST ) )
      {
        $result = 0;
        $table_id = $this->input->post('table_id');
        $user_id = $this->session->userdata('user_id');

        $data_arr = array(
          'table_name' => $this->input->post('txt_table_name'),
          'table_capacity' => $this->input->post('txt_table_capacity'),
          'user_id' => $user_id
        );

        // Check if name and number already exist
        $table_exists = $this->restaurant_table_model->check_table_exists( $user_id, $this->input->post('txt_table_name'), $table_id );

        if( $table_exists )
          $this->session->set_userdata('toast_message', 'Table already exists.');
        else if( $table_id ) // update
        {
          $result = $this->restaurant_table_model->action('update', $data_arr, $table_id);
          if( $result > 0 )
            $this->session->set_userdata('toast_message', 'Table updated successfully.');
          else
            $this->session->set_userdata('toast_message', 'Unable to update table.');
        }
        else //add
        {
          $result = $this->restaurant_table_model->action('insert', $data_arr);
          if( $result > 0 )
            $this->session->set_userdata('toast_message', 'Table added successfully.');
          else
            $this->session->set_userdata('toast_message', 'Unable to add table.');
        }
        redirect('admin/tables', 'refresh');
      }
      else //Empty
      {
        if ( $table_id )
        {
          $table_data = $this->restaurant_table_model->get_table_data( $table_id );

          if( $table_data )
          {
            $form_data = array(
              'txt_table_name' => $table_data->table_name,
              'txt_table_capacity' => $table_data->table_capacity
            );
          }
        }

        $data['form_data'] = $form_data;
        $this->template->view('add_restaurant_table', $data);
      }
  }
}

?>