<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH.'controllers/admin/Access.php';

/*
 * Programmer Name: Akash Deshmukh
 * Purpose:Member Controller
 * Date:02-09-2016
 * Dependency: membersmodel.php
 */

class Members extends Access {
    /*
     * Purpose: Constructor.
     * Date: 02-09-2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct() {
      parent::__construct();
      error_reporting();
      $this->load->model('admin/membersmodel', '', TRUE);
      $this->load->model('admin/rolemodel', 'rolemodel', TRUE);
      $this->load->model('admin/countriesmodel', '', TRUE);
      $this->load->model('admin/regionsmodel', '', TRUE);
      $this->load->model('admin/citiesmodel', '', TRUE);
    }

    /*
     * Purpose: To Load members
     * Date: 02-09-2016
     * Input Parameter: None
     * Output Parameter: None
     */

    public function index() {
      $data['activeRole'] = $this->membersmodel->getRoles();
      $data['members'] = $this->membersmodel->getData();
      $this->template->view('members', $data);
    }

    //Function to add and edit members
    public function addedit($edit_id = 0, $loyalty_id = 0) {

      $this->load->model('admin/rolemodel', '', TRUE);

      $data = array();
      $data['edit_id'] = $edit_id;
      $data['loyalty_id'] = $loyalty_id;
      $formData = array(
        'txtusername' => '',
        'txtfname' => '',
        'txtemail' => '',
        'date_of_birth' => "",
        'gender' => "",
        'country_id' => "",
        'region_id' => "",
        'city_id' => "",
        'txtcontact' => "",
        'new_password' => '',
        'conf_password' => '',
        'new_img' => '',
        'notify' => '',
        'mvg_points' => ''
        );
      $data['countriesData'] = $this->countriesmodel->getData("", '1');
      $data['regionsData'] = "";
      $data['citiesData'] = "";
      if (empty($_POST)) {

        if ($edit_id) {

          $editData = $this->membersmodel->getData($edit_id);
          //print_r($editData);die;
          $editData1 = $this->membersmodel->getLoyalty($edit_id);
          if ($editData) {
            $formData = array(
              'txtusername' => $editData->user_username,
              'txtfname' => $editData->user_first_name,
              'txtemail' => $editData->user_email,
              'date_of_birth' => date('Y-m-d', strtotime(($editData->date_of_birth))),
              'gender' => $editData->gender,
              'country_id' => 47,//$editData->cou_id,
              'region_id' => $editData->region_id,
              'city_id' => $editData->city_id,
              'txtcontact' => $editData->user_contact,
              'loyalty' => $editData->loyalty_points,
              'new_img' => $editData->user_image,
              'notify' => $editData->notification_setting,
              'mvg_points' => $editData->mvg_points
              );
            $data['regionsData'] = $this->citiesmodel->getRegionByCountry($editData->cou_id);
            $data['citiesData'] = $this->citiesmodel->getCityByRegion($editData->region_id);
          }
        }

        $data['formData'] = $formData;
        $this->template->view('addmembers', $data);
      } else {
        if (!empty($_POST['txtfname']) && !empty($_POST['txtemail'])) {

          $edit_id = $this->input->post('edit_id');

          $file_name = "";
          if (!empty($_FILES['image']['name'])) {
            $upload_data = $this->upload_members_image();

            $file_name = $upload_data['file_name'];
          }

          if ($edit_id) {
            $loyalty = $this->input->post('loyalty');
            $update_data = array(
              'user_first_name' => trim($this->input->post('txtfname')),
              'user_email' => $this->input->post('txtemail'),
              'date_of_birth' => date('Y-m-d', strtotime(($this->input->post('date_of_birth')))),
              'user_contact' => $this->input->post('txtcontact'),
              'gender' => $this->input->post('gender'),
              'country_id' => 47,//$this->input->post('country_id'),
              'region_id' => $this->input->post('region_id'),
              'city_id' => $this->input->post('city_id'),
              'notification_setting' => $this->input->post('notify'),
              'mvg_points' => trim($this->input->post('mvg_points'))
              );
            $userPwd = hash('SHA256', $_POST['conf_password']);
            $userImg = $this->input->post('new_img');
            if (!empty($_POST['conf_password'])) {
              $update_data['user_password'] = $userPwd;
            }
            if (!empty($file_name)) {
              $update_data['user_image'] = $file_name;
              $result = $this->membersmodel->action('update', $update_data, $edit_id);
              $res = $this->membersmodel->updateLoyalty($result, $loyalty);
              $this->session->set_userdata('toast_message', 'Record updated successfully.');
              $this->session->set_userdata('uploaded_img', $file_name);
              $this->session->set_userdata('redirect_to', 'admin/members');
              redirect('admin/members_crop');
            } else {
              $result = $this->membersmodel->action('update', $update_data, $edit_id);
              $res = $this->membersmodel->updateLoyalty($result, $loyalty);
              $this->session->set_userdata('toast_message', 'Record updated successfully.');
              redirect('admin/members');
            }
          } else {
            $loyalty = $this->input->post('loyalty');
            $password = hash('SHA256', $_POST['new_password']);
            $insert_data = array(
              'user_username' => trim($this->input->post('txtusername')),
              'user_password' => $password,
              'user_image' => $file_name,
              'user_first_name' => $this->input->post('txtfname'),
              'user_email' => $this->input->post('txtemail'),
              'user_contact' => $this->input->post('txtcontact'),
              'city_id' => $this->input->post('city_id'),
              'country_id' => 47,//$this->input->post('country_id'),
              'region_id' => $this->input->post('region_id'),
              'date_of_birth' => date('Y-m-d', strtotime(($this->input->post('date_of_birth')))),
              'gender' => $this->input->post('gender'),
              'user_type' => '2',
              'user_status' => '1',
              'role_id' => '3',
              );
            $result = $this->membersmodel->action('insert', $insert_data);
            $res = $this->membersmodel->insertLoyalty($result, $loyalty);
            if ($result) {
              $this->session->set_userdata('toast_message', 'Record added successfully.');
              $this->session->set_userdata('uploaded_img', $file_name);
              $this->session->set_userdata('redirect_to', 'admin/members');
              redirect('admin/members_crop');
            } else {
              $this->session->set_userdata('toast_message', 'Unable to add record.');
            }
          }
        } else {
          $this->session->set_userdata('toast_error_message', 'All fields are mandatory.');
          redirect('admin/user/addedit/' . $edit_id, 'refresh');
        }
      }

    }

    //Function to delete members
    public function delete_members($user_id = 0) {
      $update_array = array(
        'is_deleted' => '1'
        );
      $this->membersmodel->action('update', $update_array, $user_id);
      $this->session->set_userdata('toast_message', 'Record deleted successfully.');
      redirect('admin/users', 'refresh');
    }

    public function upload_members_image() {
      if (!file_exists(MEMBER_IMAGE_PATH)) {
        mkdir(MEMBER_IMAGE_PATH, 0700, true);
      }
        $file_name = stripJunk($_FILES['image']['name']); //preg_replace('/[^a-zA-Z0-9_.]/s', '', $_FILES['image']['name']);

        $config = array(
          'upload_path' => MEMBER_IMAGE_PATH,
          'allowed_types' => "jpg|jpeg",
          'overwrite' => FALSE,
          'max_size' => MAX_UPOAD_IMAGE_SIZE,
          'max_height' => "2160",
          'max_width' => "4096",
          'file_name' => $file_name
          );
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('image')) {
          $upload_data = $this->upload->data();
            // print_r($upload_data['file_name']);die;
          $data = array('file_name' => $upload_data['file_name']);
          return $data;
        } else {
          $error = array('error' => $this->upload->display_errors());
          return $error;
        }
      }

    //Function for updating the member status
      public function update_members_status() {
        $this->load->model('membersmodel', '', TRUE);
        $user_id = $this->input->post('user_id');
        $changeStatus = $this->input->post('changeStatus');
        if ($changeStatus)
          $changeStatus = '0';
        else
          $changeStatus = '1';
        $update_array = array(
          'user_status' => $changeStatus
          );
        $this->membersmodel->action('update', $update_array, $user_id);
        return 1;
      }

    //Function for checking the existence of username
      public function check_username_exists($user_id = 0) {

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
      public function check_email_exist($user_id = 0) {
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