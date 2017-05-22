<?php
/*
* Programmer Name:PK
* Purpose:Block Controller
* Date:24 May 2016
* Dependency: blockmodel.php
*/
class Setting extends CI_Controller
{
        /*
        * Purpose: Constructor.
        * Date: 24 May 2016
        * Input Parameter: None
        *  Output Parameter: None
        */
    function __construct()
        {
            parent::__construct();
           $this->load->model('admin/settingmodel', 'settingmodel', TRUE);
        }
           
        /*
        * Purpose: To Load block
        * Date: 24 May 2016
        * Input Parameter: None
        * Output Parameter: None
        */
    public function index()
    {
        if ($this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_ADMIN_TYPE || $this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_SUBADMIN_TYPE)
        {
            $data['settingData'] = $this->settingmodel->getData();
            $this->template->view('setting/addedit',$data);
        }
        else
        {
            redirect(base_url(), 'refresh');
        }
    }
    //FUNCTION FOR ADD AND EDIT BLOCK DATA
    public function addedit($edit_id = 0)
    { 
            if ($this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_ADMIN_TYPE || $this->session->userdata('user_id') && $this->session->userdata('user_type') == SEARCH_SUBADMIN_TYPE)
            {   
                $data = array();
                $data['edit_id'] = $edit_id;

                $formData = array(
                    'setting_name'=>'',
                    'setting_value'=>'',
                    'setting_parameter'=>''
                   
                );
                if(empty($_POST))
                {
                    if($edit_id)
                    {      
                        $editData = $this->settingmodel->getData($edit_id);
                        
                        if($editData){
                            $formData = array(
                                'setting_name'=>$editData->setting_name,
                                'setting_value'=>$editData->setting_value,
                                'setting_parameter'=>$editData->setting_parameter
                            );
                        }
                    }
                    $data['formData']=$formData;
                    $this->template->view('setting',$data);
                }
                else{
                    // process posted data
                    $edit_id = $this->input->post('edit_id');
                    if($edit_id =1){
                                $update_data = array(
                                    'setting_name'=> $this->input->post('setting_name'),
                                    'setting_value'=>$this->input->post('setting_value'),
                                    'setting_parameter'=>$this->input->post('setting_parameter')
                                );
                            
                        $result = $this->settingmodel->action('update',$update_data,$edit_id);
                        if($result){
                                $this->session->set_userdata('toast_message','Record updated successfully.');
                                redirect('admin/setting/addedit/1');
                        }
                        else{
                            $this->session->set_userdata('toast_message','Unable to add record.');
                        }
                    }
                    else 
                    {
                        $insert_data = array(
                            'setting_name'=> $this->input->post('setting_name'),
                            'setting_value'=>$this->input->post('setting_value'),
                            'setting_parameter'=>$this->input->post('setting_parameter')
                        );
                        $result = $this->settingmodel->action('insert',$insert_data);
                        if($result){
                            $this->session->set_userdata('toast_message','Record added successfully.');
                             redirect('admin/setting/addedit/1');
                        }
                        else{
                            $this->session->set_userdata('toast_message','Unable to add record.');
                        }
                    }
                }
            }
            else
            {
                redirect(base_url(), 'refresh');
            }
        }        
   
 }

?>