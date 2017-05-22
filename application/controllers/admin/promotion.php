<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH . 'controllers/admin/Access.php';

/* * ****************** PAGE DETAILS ******************* */

/* @Programmer  : PK
 * @Maintainer  : PK
 * @Created     : 31 Aug 2016
 * @Modified    :
 * @Description : This is gallery controller which is used
 * to show all gallery also add/edit/delete gallery.
 * ****************************************************** */
class Promotion extends Access
{
    /*
     * Purpose: Constructor.
     * Date: 31 Aug 2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct()
    {
        parent::__construct();

        $this->load->model('admin/promotionmodel', 'promotionmodel', TRUE);
        $this->load->model('webservices/notificationmodel', 'notificationmodel', TRUE);
    }

    //INDEX FUNCTION LOAD promotion MANAGEMENT VIEW       
    public function index()
    {

        $data['sliderData'] = $this->promotionmodel->getData();
        $this->template->view('promotion', $data);

    }

    //FUNCTION WHICH COUNT THE NUMBER OF promotion IN DATABASE
    public function promotion_count()
    {
        $cnt = $this->promotionmodel->promotion_count();
        echo $cnt;
    }

    //FUNCTION COUNT STATUS STATE OF promotion    
    public function promotion_state_count()
    {
        $cnt = $this->promotionmodel->promotion_state_count();
        echo $cnt;
    }

    //COMMEN FUNCTION FOR ADD EDIT promotion
    public function addedit($edit_id = 0)
    {

        $data = array();
        $data['edit_id'] = $edit_id;
        $formData = array(
            'txttitle' => '',
            'txtsubtitle' => '',
            'txturl' => '',
            'txtdate' => '',
            'txtpdf' => '',
            'image' => ''
        );
        if (empty($_POST)) {
            if ($edit_id) {
                $editData = $this->promotionmodel->getData($edit_id);
                if ($editData) {
                    $formData = array(
                        'txttitle' => $editData->promotion_title,
                        'txtsubtitle' => $editData->promotion_desc,
                        'txtpdf' => $editData->promotion_pdf,
                        'image' => $editData->promotion_image
                    );
                }
            }
            $data['formData'] = $formData;
            $this->template->view('addpromotion', $data);
        } else {
            // process posted data
            $edit_id = $this->input->post('edit_id');
            if ($edit_id) {

                if ($_FILES['txtpdf']['name'] != '') {
                    $upload_data = $this->upload_catalog_pdf();
                    if (array_key_exists('error', $upload_data)) {
                        $this->session->set_userdata('toast_error_message', $upload_data['error']);
                        redirect('promotion/addedit/' . $edit_id, 'refresh');
                    } else {
                        $update_data['promotion_pdf'] = $upload_data['file_name'];
                        $file_name1 = $upload_data['file_name'];
                        $update_array['promotion_pdf'] = $file_name1;
                    }
                }
                if ($_FILES['image']['name'] != '') {
                    $upload_data = $this->upload_promotion_image();
                    if (array_key_exists('error', $upload_data)) {
                        $this->session->set_userdata('toast_error_message', $upload_data['error']);
                        redirect('promotion/addedit', 'refresh');
                    } else {

                        $update_data = array(
                            'promotion_title' => $this->input->post('txttitle'),
                            'promotion_desc' => $this->input->post('txtsubtitle'),
                            'promotion_image' => $upload_data['file_name']
                        );
                        $file_name = $upload_data['file_name'];
                    }
                } else {
                    $update_data = array(
                        'promotion_title' => $this->input->post('txttitle'),
                        'promotion_desc' => $this->input->post('txtsubtitle')
                    );
                }

                if ($file_name1 != "") {
                    $update_data['promotion_pdf'] = $file_name1;
                }

                $result = $this->promotionmodel->action('update', $update_data, $edit_id);
                if ($result) {
                    if (!empty($file_name)) {
                        // delete old image
                        $old_image = $this->input->post('old_img');
                        $path = $old_image;
                        $ext = pathinfo($path, PATHINFO_EXTENSION);
                        $thumb_img = basename($path, "." . $ext);
                        $thumb_img = $thumb_img . "_thumb." . $ext;
                        if (file_exists(promotion_IMAGE_PATH . $old_image)) {
                            @unlink(promotion_IMAGE_PATH . $old_image);
                            @unlink(promotion_IMAGE_PATH . $thumb_img);
                        }

                        // end
                        $this->session->set_userdata('toast_message', 'Record updated successfully');
                        $this->session->set_userdata('uploaded_img', $file_name);
                        $this->session->set_userdata('redirect_to', 'admin/promotion');
                        redirect('admin/promotion_crop');
                    } else {
                        $this->session->set_userdata('toast_message', 'Record updated successfully');
                        redirect('admin/promotion');
                    }
                } else {
                    $this->session->set_userdata('toast_message', 'Unable to add record');
                }
            } else {
                $type = "PROMOTION";
                $text_message = 'New promotion "' . $this->input->post('txttitle') . '" has been added';

                $sTime = date("Y-m-d H:i:s");
                $insert_data = array(
                    'promotion_title' => $this->input->post('txttitle'),
                    'promotion_desc' => $this->input->post('txtsubtitle'),
                    'promotion_status' => 1,
                    'notification_flag' => 1,
                    'is_deleted' => 0,
                );

                if ($_FILES['txtpdf']['name'] != '') {
                    $upload_data = $this->upload_catalog_pdf();

                    if (array_key_exists('error', $upload_data)) {
                        $this->session->set_userdata('toast_error_message', $upload_data['error']);
                        redirect('promotion/addedit/', 'refresh');
                    } else {
                        $insert_data['promotion_pdf'] = $upload_data['file_name'];

                        $file_name1 = $upload_data['file_name'];
                    }
                }

                if ($_FILES['image']['name'] != '') {
                    $upload_data = $this->upload_promotion_image();
                    if (array_key_exists('error', $upload_data)) {
                        $this->session->set_userdata('toast_error_message', $upload_data['error']);
                        redirect('admin/promotion/addedit', 'refresh');
                    } else {
                        $insert_data['promotion_image'] = $upload_data['file_name'];
                        $file_name = $upload_data['file_name'];
                    }


                    $result = $this->promotionmodel->action('insert', $insert_data);
                    $this->notificationmodel->news_promotion_push_notification($type, $text_message);

                    $this->session->set_userdata('toast_message', 'Record added successfully.');
                    $this->session->set_userdata('uploaded_img', $file_name);
                    $this->session->set_userdata('redirect_to', 'admin/promotion');
                    redirect('admin/promotion_crop');
                } else {

                    $result = $this->promotionmodel->action('insert', $insert_data);
                    $this->notificationmodel->news_promotion_push_notification($type, $text_message);

                    $this->session->set_userdata('toast_message', 'Record added successfully');
                    $this->session->set_userdata('redirect_to', 'admin/promotion');
                    redirect('admin/promotion');
                }
            }
        }
    }

    function stripJunk($string)
    {
        $string = str_replace(" ", "-", trim($string));
        $string = preg_replace("/[^a-zA-Z0-9-.]/", "", $string);
        $string = strtolower($string);
        return $string;
    }

    public function upload_catalog_pdf()
    {
        if (!file_exists(PROMOTION_PDF_PATH)) {
            mkdir(PROMOTION_PDF_PATH, 0700, true);
        }
        $file_name = $this->stripJunk($_FILES['txtpdf']['name']);

        $config = array(
            'upload_path' => PROMOTION_PDF_PATH,
            'allowed_types' => "pdf",
            'overwrite' => FALSE,
            'max_size' => MAX_UPOAD_PDF_SIZE,
            'file_name' => $file_name
        );
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload('txtpdf')) {
            $upload_data = $this->upload->data();
            $data = array('file_name' => $upload_data['file_name']);
            return $data;
        } else {
            $error = array('error' => $this->upload->display_errors());
            return $error;
        }
    }


    public function upload_promotion_image()
    {
        if (!file_exists(promotion_IMAGE_PATH)) {
            mkdir(promotion_IMAGE_PATH, 0700, true);
        }
        $file_name = $this->stripJunk($_FILES['image']['name']);

        $config = array(
            'upload_path' => promotion_IMAGE_PATH,
            'allowed_types' => "jpg|png|jpeg",
            'overwrite' => FALSE,
            'max_size' => MAX_UPOAD_IMAGE_SIZE,
            'max_height' => "2160",
            'max_width' => "4096",
            'file_name' => $file_name
        );
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        if ($this->upload->do_upload('image')) {
            $upload_data = $this->upload->data();
            $data = array('file_name' => $upload_data['file_name']);
            return $data;
        } else {
            $error = array('error' => $this->upload->display_errors());
            return $error;
        }
    }

    // FUNCTION FOR DELETE promotion
    public function delete_promotion($promotion_id = 0)
    {
        $update_array = array(
            'is_deleted' => 1
        );
        $this->promotionmodel->action('update', $update_array, $promotion_id);
        $this->session->set_userdata('toast_message', 'Record deleted successfully');
        redirect('admin/promotion');
    }

    //FUNCTION FOR CHANGE STATUS OF promotion
    public function update_promotion_status()
    {
        $promotion_id = $this->input->post('promotion_id');
        $this->promotionmodel->update_status($promotion_id);
    }

    public function change_sequence($move = 'up', $mnu_id = 0)
    {
        $this->promotionmodel->change_sequence($mnu_id, $move);
        redirect('admin/promotion');
    }

}

?>