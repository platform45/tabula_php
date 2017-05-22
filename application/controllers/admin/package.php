<?php
/*
* Programmer Name:PK
* Purpose:Package Controller
* Date:12 Aug 2016
* Dependency: categorymodel.php
*/
class Package extends CI_Controller
{
        
	function __construct()
        {
            parent::__construct();

            $this->load->model('admin/packagemodel','packagemodel',TRUE);
            
        }
           
       
	public function index()
	{
            if($this->session->userdata('user_id'))
            {
                $data['packageData'] = $this->packagemodel->getData();
                
                $this->template->view('package',$data);
            }
            else
            {
                redirect('admin', 'refresh');
            }
	}
	public function update_package_status()
	{
		$p_id = $this->input->post('p_id');
		$this->packagemodel->update_package_status($p_id);
	}
	public function package_count()
	{
		$cnt=$this->packagemodel->package_count();
		echo $cnt;
	}
	/* public function check_having_pck()
	{
          //  echo 'hi';die;
		$p_id = $this->input->post("p_id");
		$this->db->select("p_id");
		$this->db->where("p_is_delete",0);
		$this->db->where("p_id",$p_id);
		$result =$this->db->get("saf_package");
		
		if($result->num_rows() > 0)
		{
			echo 0;
		}
		else
		{
			echo 1;
		}
			
	}*/
	public function delete_package($p_id = 0)
	{
		$update_array = array(
			'p_is_delete'=>1
		);
		$this->packagemodel->action('update',$update_array,$p_id);
		$this->session->set_userdata('toast_message','Record deleted successfully');
		redirect('admin/package');
	}
	
	   public function addedit($edit_id = 0)
	{
            if($this->session->userdata('user_id'))
            {
                $data = array();
                $data['edit_id'] = $edit_id;
                $formData = array(
                    'txttitle'=>'',
					'txtprice'=>''
                );
                
                if(empty($_POST))
                {                    
                    if($edit_id)
                    {
                        $editData = $this->packagemodel->getData($edit_id);
                        if($editData){
                            $formData = array(
                                'txttitle'=>$editData->p_package,
                                'txtprice'=>$editData->p_price
                            );
                        }
                    }
                    $data['formData']=$formData;    
                    $this->template->view('addpackage',$data);
                }
                else{
                    // process posted data
                    $edit_id = $this->input->post('edit_id');
                    if($edit_id){
                        
                            $update_data = array(
                                'p_package'=> mysql_real_escape_string($this->input->post('txttitle')),
                                'p_price'=> mysql_real_escape_string($this->input->post('txtprice'))
                            );
                        
                        
                        $result = $this->packagemodel->action('update',$update_data,$edit_id);
                        if($result){

                                $this->session->set_userdata('toast_message','Record updated successfully.');
                                redirect('admin/package');
                        }
                        else{
                            $this->session->set_userdata('toast_message','Unable to add record.');
                        }
                    }
                    else{
                        $insert_data = array(
                            'p_package'=> mysql_real_escape_string($this->input->post('txttitle')),
                            'p_price'=> mysql_real_escape_string($this->input->post('txtprice')),
							'p_is_active'=>1,
							'p_is_delete'=>0
                        );
                       
                        
                        $result = $this->packagemodel->action('insert',$insert_data);
                        if($result){
                            $this->session->set_userdata('toast_message','Record added successfully.');
                            redirect('admin/package');
                            //redirect('slider');
                        }
                        else{
                            $this->session->set_userdata('toast_message','Unable to add record.');
                        }
                    }
                }
            }
            else
            {
                redirect('admin', 'refresh');
            }
        }        
        
        function stripJunk($string){
            $string = str_replace(" ", "-", trim($string));
            $string = preg_replace("/[^a-zA-Z0-9-.]/", "", $string);
            $string = strtolower($string);
            return $string;
        } 
}
?>