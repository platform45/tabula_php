<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH.'controllers/admin/Access.php';


class Subscriber extends Access
{
	function __construct()
  {
    parent::__construct();

    $this->load->model('admin/subscribermodel','',TRUE);
    $this->load->library('csvimport');

    $this->load->helper('url');
    $this->load->helper('csv');
  }


  public function index()
  {
    $data['subscriberData'] = $this->subscribermodel->getData();
    $this->template->view('subscriber',$data);

  }


  public function addedit($edit_id = 0)
  {
    $data = array();
    $data['edit_id'] = $edit_id;
    $formData = array(
      'sub_first_name' => "",
      'sub_last_name' => "",
      'sub_user_mail' => ""
      );
    $data['title'] =   "Abbonato Gestione";
    if(empty($_POST))
    {
      if($edit_id)
      {
        $editData = $this->subscribermodel->getData($edit_id);

        if($editData){
          $formData = array(
            'sub_user_mail'=>$editData->sub_user_mail
            );
        }

      }

      $data['formData']=$formData; 
      $this->template->view('addsubscriber',$data);
    }
    else{
                    // process posted data

      $edit_id = $this->input->post('edit_id');

      if($edit_id){

        $update_data = array(
          'sub_email'=> $this->input->post('sub_user_mail')
          );

        $result = $this->subscribermodel->action('update',$update_data,$edit_id);
        if($result){
         $this->session->set_userdata('toast_message','Record updated successfully.');
         redirect(base_url().ADMIN.'subscriber');
       }
       else{
        $this->session->set_userdata('toast_message','Unable to update record');
      }

    }
    else{

      $insert_data = array(
        'sub_email'=> $this->input->post('sub_user_mail'),
        'sub_unsub_status'=> 1,
        'sub_status'=> 1,
        'is_deleted'=> 0
        );

      $result = $this->subscribermodel->action('insert',$insert_data);
      if($result){

        $this->session->set_userdata('toast_message','Record added successfully.');
        redirect(base_url().ADMIN.'subscriber');
      }
      else{
        $this->session->set_userdata('toast_message','Unable to add record');
      }
    }
  }

}

public function delete_subscriber($sub_id = 0)
{
 $email=$this->subscribermodel->getEmail($sub_id);
 if($sub_id){
  $data['unsubscribeData'] = $this->subscribermodel->getData1($sub_id, $email->sub_email);
}
else{
 $data['unsubscribeData'] = "User not present";
}
$this->session->set_userdata('toast_message','Record deleted successfully.');
redirect(base_url().ADMIN.'subscriber','refresh');
}
public function update_status()
{ 
  $sub_id = $this->input->post('sub_id');
  $changeStatus = $this->input->post('changeStatus');
  if($changeStatus)
    $changeStatus = 0;
  else
    $changeStatus = 1;
  $update_array = array(
    'sub_status'=>$changeStatus
    );

  $this->subscribermodel->action('update',$update_array,$sub_id);

  return 1;
}

public function update_confirm_status()
{
  $sub_id = $this->input->post('sub_id');

  $this->db->select("sub_confirm");
  $this->db->where("sub_userid",$sub_id);

  $cnf = $this->db->get("subscribermst");
  $cnf = $cnf->row();
  $cnf = $cnf->sub_confirm;

  if($cnf == 0)
  {
                    //$changeStatus = $this->input->post('changeStatus');
    $changeStatus = 1;
    $update_array = array(
      'sub_confirm'=>$changeStatus
      );

    $this->subscribermodel->action('update',$update_array,$sub_id);
    echo 1;
  }
  else
  {
    echo 2;
  }

}

public function checkemail()
{
  $sub_mail = $this->input->post("sub_mail");
  $edit_id = $this->input->post("edit_id");

  $this->db->select("sub_id");
  $this->db->from("subscribermst");
  $this->db->where("is_deleted",0);
  $this->db->where("sub_email",$sub_mail);

  if($edit_id)
    $this->db->where("sub_userid <>",$edit_id);


  $res = $this->db->get();

  if($res->num_rows() > 0)
  {
    echo 'false';
  }
  else
  {
    echo 'true';
  }
}
public function importcsv() 
{
 $data['subscriberData'] = $this->subscribermodel->get_subscribermst();
 $Emailarr = $this->subscribermodel->get_subEmail();
 $emailArrElement=array();

				$data['error'] = '';    //initialize image upload error array to empty

				$config['upload_path'] = SUBSCRIBER_CSV_PATH;
				$config['allowed_types'] = '*';
				$config['max_size'] = '10000';

				$this->load->library('upload', $config);
				
				if (!$this->upload->do_upload()) { 					
					$data['error'] = $this->upload->display_errors();

					$this->template->view('subscriber',$data);
				} 
				else 
				{
					$file_data = $this->upload->data();
					$file_path =  SUBSCRIBER_CSV_PATH.$file_data['file_name'];
					

          if($Emailarr)
          { 
           foreach($Emailarr as $e)
             $emailArrElement[] = $e['Subscriber'];
         }
         if ($this->csvimport->get_array($file_path)) { 
           $csv_array = $this->csvimport->get_array($file_path);
           $i=0;
           $j=0;
           foreach($csv_array as $key=>$row )
           {
            if(!in_array($row['Subscriber'],$emailArrElement))
            { 
              if(validate_email($row['Subscriber'])==0 || $row['Subscriber']=="")
              {
                $_SESSION['invalidCount'][] = $key; 
              }
              else{
                $insert_data = array(
                 'sub_email'=> $row['Subscriber'],
                 'sub_unsub_status'=> 1,
                 'sub_status'=> 1,
                 'is_deleted'=> 0
                 );
                $result = $this->subscribermodel->insert_csv($insert_data);
										//$this->session->set_userdata('toast_message','Csv data imported successfully.');
              }
              if(count($_SESSION['invalidCount']) > 0){
                $this->session->set_userdata('toast_message',count($_SESSION['invalidCount']).' records unable to insert becasue of invalid data.');
              }else{
                $this->session->set_userdata('toast_message','Csv data imported successfully.');  
              }
            }
            else{
									$this->session->set_userdata('toast_message','All the records already exist.');//redirect(base_url().ADMIN.'subscriber');
								}
							}
							redirect('admin/subscriber');
            }
          }
        }


        function ExportCSV()
        {
         if($this->session->userdata('user_id'))
         {
          $this->load->dbutil();
          $this->load->helper('file');
          $this->load->helper('download');
          $delimiter = ",";
          $newline = "\r\n";
          $filename = "filename_you_wish.csv";
          $query = "SELECT sub_email as Subscriber FROM tab_subscribermst WHERE is_deleted=0";
          $result = $this->db->query($query);
          $data = $this->dbutil->csv_from_result($result, $delimiter, $newline);
          force_download($filename, $data);
        }
      }

      public function downloadSample()
      {
        if($this->session->userdata('user_id'))
        {
          $this->load->dbutil();
          $this->load->helper('file');
          $this->load->helper('download');
          $data = file_get_contents( 'assets/download/sample_file.csv'); 
          $name = 'sample_file.csv';
          force_download($name, $data); 
        }
      }

    }
    ?>