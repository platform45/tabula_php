<?php
/*
* Programmer Name:SK
* Purpose:Role Controller
* Date:19 Dec 2014
* Dependency: channelmodel.php
*/
class Transaction extends CI_Controller
{
        /*
        * Purpose: Constructor.
        * Date: 19 Dec 2014
        * Input Parameter: None
        *  Output Parameter: None
        */
	function __construct()
        {
            parent::__construct();

            $this->load->model('admin/transactionmodel','transactionmodel',TRUE);
            
        }
           
        /*
        * Purpose: To Load role
        * Date: 19 Dec 2014
        * Input Parameter: None
        * Output Parameter: None
        */
	public function index()
	{
            if($this->session->userdata('user_id'))
            {
                $data['transectionData'] = $this->transactionmodel->getData();
                $this->template->view('transection_history',$data);
            }
            else
            {
                redirect('admin', 'refresh');
            }
	}
        
      public function transection_view($transect_id)
	{
            if($this->session->userdata('user_id'))
            {
                $data['transectionData'] = $this->transactionmodel->getData($transect_id);  
                $this->template->view('transection_view',$data);
            }
            else
            {
                redirect('admin', 'refresh');
            }
	}
        
      

}
?>