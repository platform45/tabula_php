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
class EmailTemplate extends CI_Controller
{
    /*
     * Purpose: Constructor.
     * Date: 17 Jan 2017
     * Input Parameter: None
     *  Output Parameter: None
     */

    function __construct()
    {
        parent::__construct();

        $this->load->model(ADMIN . 'EmailTemplateModel', '', TRUE);
        $this->load->library('form_validation');
    }

    //INDEX FUNCTION LOAD NEWS MANAGEMENT VIEW
    public function index()
    {

        if ($this->session->userdata('user_id') > 0) {
            $aData['title'] = "Email Template Management";
            $aData['aEmailTList'] = $this->EmailTemplateModel->index();
            $this->template->view('email_template_view', $aData);
        } else {
            redirect(base_url() . ADMIN);
        }

    }

    public function addedit($iEmtID = 0)
    {
        //--- CHECK USER SESSION ---//
        if ($this->session->userdata('user_id')) {
            //---- CHECK FORM POST OR NOT ----//
            if (empty($_POST)) {
                if ($iEmtID > 0) {
                    ############ EDIT DETAIL ##########
                    $aEMTDetail = $this->EmailTemplateModel->getData($iEmtID);

                    //--- PUT CHEF DETAIL INTO DATA ARRAY & PASS IT VIEW ---//
                    $aData['iEmtID'] = $iEmtID;
                    $aData['aFormData'] = $aEMTDetail[0];
                    $aData['title'] = "Edit Email Template";
                    $aData['sParentTitle'] = "Email Template Management";
                    $aData['sButtonValue'] = "Update";
                } else { ######### ADD DETAIL ##########
                    $aData['title'] = "Add Email Template";
                    $aData['sButtonValue'] = "Add";
                    $aData['sParentTitle'] = "Email Template Management";
                }
            } else {
                $iEmtID = $this->input->post('hidEmtID');
                $this->form_validation->set_rules('email_from', 'Email From', 'trim|required|valid_email|xss_clean|strip_tags');
                $this->form_validation->set_rules('email_name', 'Email Title', 'trim|required|xss_clean|strip_tags');
                $this->form_validation->set_rules('email_subject', 'Email Subject', 'trim|required|xss_clean|strip_tags');
                $this->form_validation->set_rules('email_body', 'Email Body', 'trim|required');
                if ($this->form_validation->run() == FALSE) {
                    $this->session->set_userdata("toast_error_message", validation_errors());
                    redirect("admin/emailtemplate/addedit/" . $iEmtID);
                } else {
                    //***** PROCESS FORM DATA ****//
                    $iEmtID = $this->input->post('hidEmtID');
                    $aData['title'] = "Edit Email Template";
                    $aData['sButtonValue'] = "Update";
                    $aData['sParentTitle'] = "Email Template Management";
                    $aData['iEmtID'] = $iEmtID;
                    $aChefDetail = $this->EmailTemplateModel->getData($iEmtID);
                    if (TRUE) {
                        $aPostData = array(
                            'email_from' => mysql_real_escape_string($this->input->post('email_from')),
                            'email_name' => trim($this->input->post('email_name')),
                            'email_subject' => trim($this->input->post('email_subject')),
                            'email_body' => ($this->input->post('email_body'))
                        );
                        if ($iEmtID > 0) {
                            $aUpdateData = array(
                                'email_from' => mysql_real_escape_string($this->input->post('email_from')),
                                'email_name' => trim($this->input->post('email_name')),
                                'email_subject' => trim($this->input->post('email_subject')),
                                'email_body' => ($this->input->post('email_body'))
                            );
                            $aUpdateRecord = $this->EmailTemplateModel->UpdateDetail($iEmtID, $aUpdateData);
                            redirect('admin/emailtemplate');
                        } else {
                            $iAddRecord = $this->EmailTemplateModel->AddDetail($aPostData);
                            switch ($iAddRecord) {
                                case 1:
                                    $this->session->set_userdata('toast_message', 'Chef added successfully.');
                                    redirect('admin/emailtemplate');
                                    break;
                                case 2:
                                    $this->session->set_userdata('toast_message', 'ID Number already exist.');
                                    $aData['aFormData'] = $aPostData;
                                    break;
                                case 3:
                                    $this->session->set_userdata('toast_message', 'Email address already exist.');
                                    $aData['aFormData'] = $aPostData;
                                    break;
                            }
                        }
                    } else {
                        $aChefDetail = $this->EmailTemplateModel->getData($iEmtID);
                        //--- PUT CHEF DETAIL INTO DATA ARRAY & PASS IT VIEW ---//
                        $aData['iEmtID'] = $iEmtID;
                        $aData['aFormData'] = $aChefDetail[0];
                        $aData['title'] = "Edit Chef";
                        $aData['sButtonValue'] = "Update";
                        $aData['sParentTitle'] = "Chef Management";
                    }
                }
            }
        } else {
            redirect('admin/emailtemplate');
        }
        //End user id session if condition.
        $this->template->view('add_email_template_view', $aData);
    }
}

?>