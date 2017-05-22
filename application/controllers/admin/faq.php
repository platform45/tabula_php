<?php

/*
 * Programmer Name:Akash Deshmukh
 * Purpose:Role Controller
 * Date:14 Sept 2016
 * Dependency: faqmodel.php
 */

class Faq extends CI_Controller {
    /*
     * Purpose: Constructor.
     * Date: 14 Sept 2016
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct() {
        parent::__construct();

        $this->load->model('admin/faqmodel', '', TRUE);
    }

    /*
     * Purpose: To Load role
     * Date: 14 Sept 2016
     * Input Parameter: None
     * Output Parameter: None
     */

    public function index() {
        if ($this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_ADMIN_TYPE || $this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_SUBADMIN_TYPE) {
            $data['faqData'] = $this->faqmodel->getData();
            $data['cnt'] = $this->faqmodel->faq_count();
            $this->template->view('faq', $data);
        } else {
            redirect('admin', 'refresh');
        }
    }

    /*
     * Purpose: To add/edit role
     * Date: 14 Sept 2016
     * Input Parameter: None
     * Output Parameter: None
     */

    public function addedit($edit_id = 0) {
        if ($this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_ADMIN_TYPE || $this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_SUBADMIN_TYPE) {
            $data = array();
            $data['edit_id'] = $edit_id;
            $formData = array(
                'faq_id' => "",
                'faq_sequenceno' => "",
                'faq_question' => "",
                'faq_answer' => "",
                'status' => "",
                'is_delete' => ""
            );

            if (empty($_POST)) {
                if ($edit_id) {
                    $editData = $this->faqmodel->getData($edit_id);
                    if ($editData) {
                        $formData = array(
                            'faq_id' => $editData->faq_id,
                            'faq_sequenceno' => $editData->faq_sequenceno,
                            'faq_question' => stripslashes($editData->faq_question),
                            'faq_answer' => $editData->faq_answer,
                            'status' => $editData->status,
                            'is_delete' => $editData->is_delete
                        );
                    }
                }
                $data['formData'] = $formData;
                $this->template->view('addfaq', $data);
            } else {
                if ($this->form_validation->run('frmFaq')) {
                    // process posted data
                    $edit_id = $this->input->post('edit_id');

                    if ($edit_id) {
                        $update_data = array(
                            'faq_question' => $this->input->post('txtquestion_en'),
                            'faq_answer' => $this->input->post('txtanswer_en')
                        );
                        $result = $this->faqmodel->action('update', $update_data, $edit_id);
                        if ($result) {
                            $this->session->set_userdata('toast_message', 'Record updated successfully.');
                            redirect('admin/faq');
                        } else {
                            $this->session->set_userdata('toast_message', 'Unable to add record.');
                        }
                    } else {

                        $sequence = $this->faqmodel->getNewSequence();
                        $insert_data = array(
                            'faq_sequenceno' => $sequence,
                            'faq_question' => $this->input->post('txtquestion_en'),
                            'faq_answer' => $this->input->post('txtanswer_en'),
                            'status' => '1',
                            'is_delete' => '0'
                        );
                        $result = $this->faqmodel->action('insert', $insert_data);
                        if ($result) {
                            $this->session->set_userdata('toast_message', 'Record added successfully.');
                            redirect('admin/faq');
                        } else {
                            $this->session->set_userdata('toast_message', 'Unable to add record.');
                        }
                    }
                } else {
                    $data['edit_id'] = $edit_id;
                    $data['formData'] = $formData;
                    $this->template->view('addfaq', $data);
                }
            }
        } else {
            redirect('admin', 'refresh');
        }
    }

    public function delete_faq($faq_id = 0) {
        $update_array = array(
            'is_delete' => '1'
        );
        $this->faqmodel->action('update', $update_array, $faq_id);
        $this->session->set_userdata('toast_message', 'Record deleted successfully.');
        redirect('admin/faq', 'refresh');
    }

    public function update_status() {
        $this->load->model('faqmodel', '', TRUE);
        $faq_id = $this->input->post('faq_id');
        $changeStatus = $this->input->post('changeStatus');
        if ($changeStatus)
            $changeStatus = '0';
        else
            $changeStatus = '1';
        $update_array = array(
            'status' => $changeStatus
        );

        $this->faqmodel->action('update', $update_array, $faq_id);
        return 1;
    }

    public function change_sequence($move = 'up', $mnu_id = 0) {
        $this->faqmodel->change_sequence($mnu_id, $move);
        redirect('admin/faq');
    }

}

?>