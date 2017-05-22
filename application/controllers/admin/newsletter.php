<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH.'controllers/admin/Access.php';

/*
 * Programmer Name:SK
 * Purpose:Locum Controller
 * Date:03-02-2015
 * Dependency: newslettermodel.php
 */

class Newsletter extends Access {
    /*
     * Purpose: Constructor.
     * Date: 03-02-2015
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct() {
        parent::__construct();
        $this->load->model('admin/newslettermodel', '', TRUE);
    }

    /*
     * Purpose: To Load locum
     * Date: 03-02-2015
     * Input Parameter: None
     * Output Parameter: None
     */

    public function index() {
       
            $data['newsletterData'] = $this->newslettermodel->getData();
            $this->template->view('newsletter', $data);
        
    }

    /*
     * Purpose: To add/edit locum
     * Date: 03-02-2015
     * Input Parameter: None
     * Output Parameter: None
     */

    public function addedit($edit_id = 0) {
       
            $data = array();
            $data['edit_id'] = $edit_id;
            $formData = array(
                'newsletter_title' => "",
                'newsletter_content' => ""
            );

            $data['subscriberData'] = $this->newslettermodel->getSubscriberData();
            $emailMap = array();
            if ($data['subscriberData']) {
                foreach ($data['subscriberData'] as $value) {
                    $emailMap[$value['sub_id']] = $value['sub_email'];
                }
            }
            if (empty($_POST)) {
                if ($edit_id) {
                    $editData = $this->newslettermodel->getData($edit_id);

                    if ($editData) {
                        $formData = array(
                            'newsletter_title' => $editData->newsletter_title,
                            'newsletter_content' => $editData->newsletter_content
                        );
                    }
                }

                $data['formData'] = $formData;
                $this->template->view('addnewsletter', $data);
            } else {
                // process posted data

                $edit_id = $this->input->post('edit_id');
                $btn = $this->input->post("action");
                $chbData = $this->input->post("chbemail");
                if ($edit_id) {


                    $update_data = array();
                    if ($btn == 'Update') {
                        $update_data = array(
                            'newsletter_title' => $this->input->post('newsletter_title'),
                            'newsletter_content' => $this->input->post('newsletter_content'),
                            'newsletter_submitted_date' => date("Y-m-d"),
                            'newsletter_modified_date' => date("Y-m-d")
                        );
                        $this->session->set_userdata('toast_message', 'Record updated successfully');
                    } else if ($btn == 'Send') {
                        $update_data = array(
                            'newsletter_title' => $this->input->post('newsletter_title'),
                            'newsletter_content' => $this->input->post('newsletter_content'),
                            'newsletter_submitted_date' => date("Y-m-d"),
                            'newsletter_modified_date' => date("Y-m-d"),
                            'newsletter_send_date' => date("Y-m-d")
                        );
                    }

                    $result = $this->newslettermodel->action('update', $update_data, $edit_id);
                    if ($result) {

                        if ($btn == 'Send') {

                            //Record is inserted, now send mails.

                            $this->load->library('email');
                            $config['mailtype'] = 'html';
                            $this->email->initialize($config);


                            $emailTemplate = get_email_template("Newsletter"); // Get Newsletter template
                            if ($chbData) {
                                foreach ($chbData as $key => $value) {
                                    $emailID = $emailMap[$value];
                                    $txtMessage = $this->input->post('newsletter_content');
                                    $strParam = array(
                                        '{MESSAGE}' => $txtMessage,
                                        '{UNSUBSCRIBE}' => "<a href='" . $this->config->item('base_url') . "admin/unsubscribe/newsletter/" . $value . "' target='_blank'>Click Here to unsubscribe</a>",
                                        '{EMAIL_FOOTER_TITLE}' => $this->config->item('email_footer_title')
                                    );
                                    $txtMessageStr = mergeContent($strParam, $emailTemplate->email_body);
                                    $this->email->from('genknooz4@gmail.com', 'Tabula');
                                    $this->email->to($emailID);
                                    $this->email->subject("Tabula - Newsletter");

                                    $this->email->message($txtMessageStr);
                                    $this->email->send();
                                    echo $this->email->print_debugger();
                                }
                                $this->session->set_userdata('toast_message', 'Newsletter sent successfully');
                            } else {
                                $this->session->set_userdata('toast_error_message', 'No subscriber to send mail.');
                            }
                        }



                        redirect('admin/newsletter');
                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record');
                    }
                } else {

                    if ($btn == 'Save') {
                        $insert_data = array(
                            'newsletter_title' => $this->input->post('newsletter_title'),
                            'newsletter_content' => $this->input->post('newsletter_content'),
                            'newsletter_submitted_date' => date("Y-m-d"),
                            'newsletter_modified_date' => date("Y-m-d"),
                            'newsletter_active' => 1,
                            'newsletter_deleted' => 0
                        );
                        $this->session->set_userdata('toast_message', 'Record added successfully');
                    } else if ($btn == 'Send') {
                        $insert_data = array(
                            'newsletter_title' => $this->input->post('newsletter_title'),
                            'newsletter_content' => $this->input->post('newsletter_content'),
                            'newsletter_submitted_date' => date("Y-m-d"),
                            'newsletter_modified_date' => date("Y-m-d"),
                            'newsletter_send_date' => date("Y-m-d"),
                            'newsletter_active' => 1,
                            'newsletter_deleted' => 0
                        );
                    }

                    $result = $this->newslettermodel->action('insert', $insert_data);

                    if ($result) {
                        if ($btn == 'Send') {
                            //Record is inserted, now send mails.

                            $this->load->library('email');
                            $config['mailtype'] = 'html';
                            $this->email->initialize($config);


                            $emailTemplate = get_email_template("Newsletter");

                            if ($chbData) {
                                foreach ($chbData as $key => $value) {
                                    $emailID = $emailMap[$value];

                                    $txtMessage = $this->input->post('newsletter_content');
                                    $strParam = array(
                                        '{MESSAGE}' => $txtMessage,
                                        '{UNSUBSCRIBE}' => "<a href='" . $this->config->item('base_url') . "admin/unsubscribe/newsletter/" . $value . "' target='_blank'>Click Here to unsubscribe</a>",
                                        '{EMAIL_FOOTER_TITLE}' => $this->config->item('email_footer_title')
                                    );
                                    $txtMessageStr = mergeContent($strParam, $emailTemplate->email_body);
                                    $this->email->from('genknooz4@gmail.com', 'Tabula');
                                    $this->email->to($emailID);
                                    $this->email->subject("Tabula - Newsletter");


                                    $this->email->message($txtMessageStr);
                                    $this->email->send();
                                }
                                $this->session->set_userdata('toast_message', 'Newsletter sent successfully');
                            } else {
                                $this->session->set_userdata('toast_error_message', 'No subscriber to send mail.');
                            }
                        }

                        redirect('admin/newsletter');
                    } else {
                        $this->session->set_userdata('toast_message', 'Unable to add record');
                    }
                }
            }
        
    }

    public function delete_newsletter($user_id = 0) {
        $update_array = array(
            'newsletter_deleted' => 1
        );
        $this->newslettermodel->action('update', $update_array, $user_id);

        $this->session->set_userdata('toast_message', 'Record deleted successfully');
        redirect('admin/newsletter', 'refresh');
    }

}

?>