<?php
/* * ****************** PAGE DETAILS ******************* *
 * @Programmer  : ANSHUMAN DESHPANDE
 * @Maintainer  : ANSHUMAN DESHPANDE
 * @Created     : 1 Oct 2015
 * @Modified    : 
 * @Description : Push_notification controller.
 * ****************************************************** */
class Push_notification extends CI_Controller
{
        /**
	*CONSTRUCTOR
	*@Param:
	*@Return:
	*/
	function __construct()
        {
            parent::__construct();
            $this->load->model('admin/push_notification_model','',TRUE);
        }
           
        /**
	*Function To Load push notification
	*@Param:
	*@Return:
	*/
	public function index()
	{
            if($this->session->userdata('user_id'))
            {
                if($this->input->post())
                {
                    if($this->input->post('txtnotification'))
                    {
                        $insert_data = array(
                            'push_message'=> $this->input->post('txtnotification'),
                            'push_message_added_to_queue'=> 0
                        );
                        $this->db->set('push_message_date', 'NOW()', FALSE); // THIS SETS THE TIME ON SERVER SIDE...
                        
                        
                        //mysql_query("INSERT INTO jinx_pushnotification (push_message, push_message_added_to_queue) VALUES ('".$this->input->post('txtnotification')."', 0)");
                        $this->db->insert('pushnotification',$insert_data);
                        $result = $this->db->insert_id();
                        if($result)
                        {
                            $this->session->set_userdata('toast_message','Push notification has been added to the queue. Sending push notifications will start shortly.');
                        }
                        else
                        {
                            $this->session->set_userdata('toast_error_message','Failed to queue push notification.');
                        }
                        redirect("admin/push_notification");
                    }
                    else
                    {
                        $this->session->set_userdata('toast_error_message','Please enter notification text.');
                    }
                }
                $this->template_admin->view('addpush_notification');
            }
            else
            {
                redirect('admin', 'refresh');
            }
            
	}
        
        
        /**
	*Function to add members to the push notification queue
	*@Param: - 
	*@Return: -
	*/
        public function add_members_to_queue()
        {
            $this->push_notification_model->queue_all_users();
        }
        
        
        /**
	*Function to add members to the push notification queue
	*@Param: - 
	*@Return: -
	*/
        public function send_notifications()
        {
            $this->push_notification_model->send_notifications_to_users();
        }
        
        
}
?>