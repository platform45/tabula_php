<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH.'controllers/admin/Access.php';
/*
 * Programmer Name:Akash Deshmukh
 * Purpose:content Controller
 * Date:02 Sept 2016
 * Dependency: cmsmenumodel.php
 */

class Cmsmenu extends Access {
    /*
     * Purpose: Constructor.
     * Date: 02 Sept 2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct() {
      parent::__construct();

      $this->load->model('admin/cmsmenumodel', '', TRUE);
    }

    /*
     * Purpose: To Load content
     * Date: 02 Sept 2016
     * Input Parameter: None
     * Output Parameter: None
     */

    public function index() {

        if ($this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_ADMIN_TYPE || $this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_SUBADMIN_TYPE) {
            $data['contentMenuData'] = $this->cmsmenumodel->getData();
            $data['contentLeftMenuData'] = $this->cmsmenumodel->getData(0, 'Left');
            $data['parentMenu'] = $this->cmsmenumodel->getDropdownData();
            //$data['counterData'] = $this->cmsmenumodel->getApplicationSetting('counterDate');
            $this->template->view('cmsmenu', $data);
        } else {
            redirect('admin', 'refresh');
        }
    }

    //Function to get the menu
    public function getMenuHtml($mnuArr, $first = '', $last = '') {
      ?>
      <li class="list-group-item level_<?php echo $mnuArr['mnu_level']; ?> <?php echo $first; ?>">
        <?php echo $mnuArr['mnu_menu_name']; ?>
        <?php
        $delete = 0;
        if ($mnuArr['mnu_parent_menu_id']) {
          $delete = '1';
          ?>
          <div style="float: right; padding: 0 20px;">
            <a class="delete_button pull-right" title="Delete" data-id="<?php echo $mnuArr['mnu_menuid']; ?>" data-toggle="modal" role="button" id="dl_<?php echo $mnuArr['mnu_menuid']; ?>" href="#myModal">
              <i class="fa fa-times-circle-o fa-2x"></i>
            </a>
          </div>
          <?php } ?>
          <div style="float: right; padding: 0 20px;">
            <a data-id="<?php echo $mnuArr['mnu_menuid']; ?>" style="margin-right:0px;" title="Content page" class="contentpage pull-right" id="cn_<?php echo $mnuArr['mnu_menuid']; ?>" href="<?php echo base_url() . 'admin/cmsmenu/content/' . $mnuArr['mnu_menuid']; ?>">
              <i class="fa fa-file-text fa-2x"></i>
            </a>
          </div>

        </li>

        <?php
        if (array_key_exists('child', $mnuArr)) {
          $first = 'first';
          $count = 0;
          $last = '';
          foreach ($mnuArr['child'] as $mnu) {
            $count++;
            if (count($mnuArr['child']) == $count) {
              if ($first != 'first')
                $first = 'last';
              else
                $first = 'single';
            }
            $this->getMenuHtml($mnu, $first);
            $first = '';
          }
        }
      }

    //Function to get menu items
      public function getOtherMenuHtml($mnuArr, $first = '', $last = '') {
        ?>
        <li class="list-group-item level_<?php echo $mnuArr['mnu_level']; ?> <?php echo $first; ?>">
          <?php echo $mnuArr['mnu_menu_name']; ?>
          <?php
          $delete = 0;
          if ($mnuArr['mnu_parent_menu_id']) {
            $delete = 1;
            ?>
            <?php } ?>

            <?php if ($mnuArr['mnu_menuid'] == 143) { ?>
            <div style="float: right; padding: 0 20px;display: inline-block;min-width: 60px;">
            </div>
            <?php } else { ?>
            <div style="float: right; padding: 0 20px;display: inline-block;min-width: 60px;">&nbsp;</div>
            <?php } ?>

            <div style="float: right; padding: 0 20px;">
              <a data-id="<?php echo $mnuArr['mnu_menuid']; ?>" style="margin-right:0px;" title="Content page" class="contentpage pull-right" id="cn_<?php echo $mnuArr['mnu_menuid']; ?>" href="<?php echo base_url() . 'cmsmenu/content/' . $mnuArr['mnu_menuid']; ?>">
                <i class="fa fa-file-text fa-2x"></i>
              </a>
            </div>

        </li>

          <?php
          if (array_key_exists('child', $mnuArr)) {
            $first = 'first';
            $count = 0;
            $last = '';
            foreach ($mnuArr['child'] as $mnu) {
              $count++;
              if (count($mnuArr['child']) == $count) {
                if ($first != 'first')
                  $first = 'last';
                else
                  $first = 'single';
              }
              $this->getMenuHtml($mnu, $first);
              $first = '';
            }
          }
        }

        public function addDash($no_of_dash = 0) {
          $str = "";
          for ($i = 1; $i < $no_of_dash; $i++) {
            $str .= "--";
          }
          return $str;
        }

    //Function to get options
        public function getSelectOption($mnu_row) {
          if ($mnu_row['mnu_menuid'] == 3) {

          } else {
            ?>
            <option value="<?php echo $mnu_row['mnu_menuid']; ?>"><?php echo $this->addDash($mnu_row['mnu_level']) . $mnu_row['mnu_menu_name']; ?></option>
            <?php
            if (array_key_exists('child', $mnu_row)) {
              foreach ($mnu_row['child'] as $mnu) {
                $this->getSelectOption($mnu);
              }
            }
          }
        }

        private $allChildStr = "";

        public function getAllChildId() {
          $mnu_id = $this->input->post('mnu_id');
          $this->allChildStr = "";
          $allChild = $this->cmsmenumodel->getData($mnu_id);
          $resultData = $this->getChildId($allChild);
          $returnArr = explode('-', $this->allChildStr);
          echo json_encode($returnArr);
        }

        public function getChildId($allChild) {
          foreach ($allChild as $mnu) {
            $mnu_id = $mnu['mnu_menuid'];
            $this->allChildStr .= $mnu_id . '-';
            if (is_array($mnu))
              if (array_key_exists('child', $mnu)) {
                $this->getChildId($mnu['child']);
              }
            }
          }

          public function addmenu() {

            // get menu level if parent set else it is default 1
            $mnu_level = 1;
            $mnu_parent_id = $this->input->post('selParentMenu');
            if (!empty($mnu_parent_id)) {

              $mnu_level = $this->cmsmenumodel->getMenuLevel($mnu_parent_id);
            }
            $result = 0;
            $edit_id = $this->input->post('edit_id');
            $my_current_level = $this->input->post('my_current_level');
            $all_child_ids = $this->input->post('all_child_ids');

            if ($edit_id) {
              $update_data = array(
                'mnu_parent_menu_id' => $mnu_parent_id,
                'mnu_menu_name' => $this->input->post('txtMenuTitle'),
                'mnu_is_content' => 1,
                'mnu_status' => 1,
                'mnu_level' => $mnu_level,
                'mnu_type' => $this->input->post('selMenuType'),
                'mnu_modified_on' => date('Y-m-d H:i:s'),
                'mnu_modified_by' => $this->session->userdata('user_id')
                );
              $diff_in_level = 0;

              $result = $this->cmsmenumodel->action('update', $update_data, $edit_id);

              if ($result) {
                if ($my_current_level != $mnu_level) {
                  $diff_in_level = $mnu_level - $my_current_level;
                }
                if ($diff_in_level != 0) {
                  if (strlen($all_child_ids)) {
                    $all_child_ids = trim($all_child_ids, ",");
                    $this->cmsmenumodel->update_level($diff_in_level, $all_child_ids);
                  }
                }
              }
            } else {
              $mnu_type = $this->input->post('selMenuType');
              $sequence = $this->cmsmenumodel->getNewSequence($mnu_parent_id, $mnu_type);
              $insert_data = array(
                'mnu_parent_menu_id' => $mnu_parent_id,
                'mnu_menu_name' => $this->input->post('txtMenuTitle'),
                'mnu_is_content' => 1,
                'mnu_status' => 1,
                'mnu_level' => $mnu_level,
                'mnu_type' => $mnu_type,
                'mnu_sequence' => $sequence,
                'mnu_created_on' => date('Y-m-d H:i:s'),
                'mnu_created_by' => $this->session->userdata('user_id')
                );
              $result = $this->cmsmenumodel->action('insert', $insert_data);
            }

            if ($result) {
              if ($edit_id)
                $this->session->set_userdata('toast_message', 'Record updated successfully.');
              else
                $this->session->set_userdata('toast_message', 'Record added successfully');
              redirect('admin/cmsmenu');
            }
            else
              redirect('admin/cmsmenu', 'refresh');


          }

    //Function to update counter
          public function updateCounter() {

            if (!empty($_POST)) {
              $counterTitle = $this->input->post('txtCounterTitle');
              $counterDate = $this->input->post('txtCounterDate');
              $visibleYesNo = $this->input->post('chkVisibleYesNo');
              $updateArray = array('app_key_title' => $counterTitle, 'app_val' => $counterDate, 'app_active' => $visibleYesNo);
              $this->db->update('rggc_application_settings', $updateArray, array('app_key' => 'counterDate'));
              $this->session->set_userdata('toast_message', 'Record updated successfully.');
              redirect('admin/cmsmenu');
            }
            else
              redirect('admin/cmsmenu', 'refresh');
          }


    //Function to change the menu sequence
          public function change_sequence($move = 'up', $mnu_id = 0) {
            $this->cmsmenumodel->change_sequence($mnu_id, $move);
            redirect('admin/cmsmenu');
          }

    //Function to delete menu
          public function delete_mnu() {
            $mnu_id = $this->input->post('mnu_id');
            $update_array = array(
              'is_deleted' => 1
              );
            $this->cmsmenumodel->action('update', $update_array, $mnu_id);
            $this->session->set_userdata('toast_message', 'Record deleted successfully');
            echo 1;
          }

    //Function to update the men status
          public function update_mnu_status() {
            $mnu_menuid = $this->input->post('mnu_id');
            $changeStatus = $this->input->post('ele_status');
            $ele_all_child = $this->input->post('ele_all_child');
            if ($changeStatus)
              $changeStatus = 0;
            else
              $changeStatus = 1;
            $update_array = array(
              'mnu_status' => $changeStatus
              );
            $ele_all_child = trim($ele_all_child, ',');
            if (strlen($ele_all_child) > 0) {
              $ele_all_child = $ele_all_child . ',' . $mnu_menuid;
            }else
            $ele_all_child = $mnu_menuid;
            $this->cmsmenumodel->update_menu_status($update_array, $ele_all_child);
            $this->session->set_userdata('toast_message', 'Status updated successfully');
            echo 1;
          }

    //CONTENT CMS PAGES 
          public function content($edit_menu_id = 0) {

            $data = array();
            $data['edit_menu_id'] = $edit_menu_id;
            if (!empty($_POST)) {
              $contentType = $this->input->post('content_radio');
              $edit_menu_id = $this->input->post('hidd_edit_menu_id');
              $link = str_replace(" ", "-", $this->input->post('en_txtpageurl'));
              $insert_data = array();
              $pdf_file = "";

              $sUpdateLink = "";
              if ($edit_menu_id == 143) {
                $sUpdateLink = prep_url($link);
              } else {
                $sUpdateLink = str_replace("", "-", $link);
              }

              switch ($contentType) {
                case 'content':
                $insert_data = array(
                  'cont_menuid' => $edit_menu_id,
                  'cont_browser_title' => $this->input->post('en_txtbrowsertitle'),
             //     'cont_page_title' => $this->input->post('en_txtpagetitle'),
                  'cont_url_name' => $sUpdateLink,
                  'cont_meta_description' => $this->input->post('en_txtmetadescription'),
                  'cont_keywords' => $this->input->post('en_txtkeywords'),
                  'cont_content' => $this->input->post('en_txtContent'),
                  'cont_content_type' => 1,
                  'cont_created_on' => date('Y-m-d H:i:s'),
                  'cont_created_by' => $this->session->userdata('user_id'),
                  'cont_modified_on' => date('Y-m-d H:i:s'),
                  'cont_modified_by' => $this->session->userdata('user_id')
                  );
                break;
                case 'pdf':
                if (!empty($_FILES['pdffile']['name'])) {
                  $upload_data = $this->upload_pdf();
                  $pdf_file = $upload_data['file_name'];
                  $insert_data = array(
                    'cont_menuid' => $edit_menu_id,
                    'cont_pdf_file' => $pdf_file,
                    'cont_content_type' => 2
                    );
                } else {
                  $insert_data = array(
                    'cont_menuid' => $edit_menu_id,
                    'cont_content_type' => 2
                    );
                }

                break;
                case 'url':
                $insert_data = array(
                  'cont_menuid' => $edit_menu_id,
                  'cont_content_type' => 3,
                  'cont_external_url' => $this->input->post('txtwebpageurl')
                  );
                break;
              }

              if (!empty($insert_data)) {

                $result = $this->cmsmenumodel->action('insert_update_content', $insert_data);
                $this->session->set_userdata('toast_message', 'Record updated successfully.');

              }
              redirect('admin/cmsmenu', 'refresh');

              redirect('admin/cmsmenu', 'refresh');
            } else {
              $data['edit_menu_id'] = $edit_menu_id;
              $data['content'] = $this->cmsmenumodel->getContentData($edit_menu_id);
              $this->template->view('content', $data);
            }

          }

    //Function for uploading pdf file
          public function upload_pdf() {
            if (!file_exists(CONTENT_PDF_FILE)) {
              mkdir(CONTENT_PDF_FILE, 0700, true);
            }
            $file_name = $_FILES['pdffile']['name'];
            $file_name = preg_replace('/[^a-zA-Z0-9_.]/s', '', $file_name);
            $file_name = time() . "_" . $file_name;
            $config = array(
              'upload_path' => CONTENT_PDF_FILE,
              'allowed_types' => "pdf",
              'overwrite' => FALSE,
              'file_name' => $file_name
              );
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('pdffile')) {
              $upload_data = $this->upload->data();
              $data = array('file_name' => $file_name);
              return $data;
            } else {
              $error = array('error' => $this->upload->display_errors());
              return $error;
            }
          }

    //Function for checking the existence if the URL
          public function check_url_exists($id = FALSE) {
            if ($id === FALSE) {
              $link = $this->input->post('link');
              if ($link != '') {
                $url_exists = $this->cmsmenumodel->check_url_exists($link);
                if ($url_exists && (strcmp($url_exists, $link) != 0))
                  echo json_encode(TRUE);
                else
                  echo json_encode(FALSE);
              }
              else
                echo json_encode(TRUE);
            }
            else {
              $link = $this->input->post('link');
              if ($link != '') {
                $url_exists = $this->cmsmenumodel->check_url_exists($link, $id);
                if ($url_exists && (strcmp($url_exists, $link) != 0))
                  echo json_encode(TRUE);
                else
                  echo json_encode(FALSE);
              }
              else
                echo json_encode(TRUE);
            }
          }

    //Function call for image upload
          function stripJunk($string) {
            $string = str_replace(" ", "-", trim($string));
            $string = preg_replace("/[^a-zA-Z0-9-.]/", "", $string);
            $string = strtolower($string);
            return $string;
          }

    //Function for uploading an image
          public function upload_header_image() {
            if (!file_exists(SLIDER_HEADER_PATH)) {
              mkdir(SLIDER_HEADER_PATH, 0700, true);
            }
        $file_name = $this->stripJunk($_FILES['image']['name']); //preg_replace('/[^a-zA-Z0-9_.]/s', '', $_FILES['image']['name']);

        $config = array(
          'upload_path' => SLIDER_HEADER_PATH,
          'allowed_types' => "jpg|png|jpeg",
          'overwrite' => FALSE,
          'max_size' => MAX_UPOAD_IMAGE_SIZE,
          'max_height' => "2160",
          'max_width' => "4096",
          'file_name' => $file_name
          );
        $this->load->library('upload', $config);
        if ($this->upload->do_upload('image')) {
          $upload_data = $this->upload->data();
          $data = array('file_name' => $upload_data['file_name']);
          return $data;
        } else {
          $error = array('error' => $this->upload->display_errors());
          return $error;
        }
      }

    // END OF CMS PAGES
      public function delete_header_image($id) {
        $result = $this->cmsmenumodel->delete_heder_image($id);
        echo $result;
      }

    }
    ?>