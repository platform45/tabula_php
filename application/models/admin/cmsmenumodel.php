<?php
class Cmsmenumodel extends CI_Model
{
    private $childIds = '';
    public function __construct()
    {
        parent::__construct();
    }
    public function getData($parent_id = 0,$menuType = 'Top')
    {
        $menuList = array();
        $this->db->select("mnu_menuid,mnu_menu_name,mnu_parent_menu_id,mnu_level,mnu_type,mnu_sequence,mnu_url,mnu_status,is_deleted",FALSE);

        $this->db->where(
            array(

                'mnu_parent_menu_id' => $parent_id,
                'mnu_type' => $menuType
                ));
        $this->db->order_by('mnu_sequence','asc');
        $result = $this->db->get('menumst');
        if($result->num_rows()){
            $resultMenu = $result->result_array();
            foreach($resultMenu as $ele){
                $child_parent_id = $ele['mnu_menuid'];
                if($child_parent_id)
                {
                    $childArr = $this->getData($child_parent_id);
                    if($childArr){
                        $ele['child'] = $childArr;
                    }
                    array_push($menuList,$ele);
                }
            }
        }
        return $menuList;
    }
            // only parent menues of first level selcted here..
           /* public function getDropdownData($parent_id = 0,$menuType = 'Top')
            {
                $menuList = array();
                $this->db->select("mnu_menuid,mnu_menu_name,mnu_parent_menu_id,mnu_level,mnu_type,mnu_sequence,mnu_url,mnu_status,is_deleted",FALSE);
                
                $this->db->where(
                            array(
                                'is_deleted' => 0,
                                //'mnu_parent_menu_id' => $parent_id,
                                'mnu_level <='=>2,
                                'mnu_type' => $menuType
                            ));
                $this->db->order_by('mnu_sequence','asc');
                $result = $this->db->get('menumst');
                if($result->num_rows()){
                    $resultMenu = $result->result_array();
                    foreach($resultMenu as $ele){
                        array_push($menuList,$ele);
                    }
                }
                return $menuList;
            }*/
            
            public function getDropdownData($parent_id = 0,$menuType = 'Top')
            {
                $menuList = array();
                $this->db->select("mnu_menuid,mnu_menu_name,mnu_parent_menu_id,mnu_level,mnu_type,mnu_sequence,mnu_url,mnu_status,is_deleted",FALSE);
                
                $this->db->where(
                    array(
                        'mnu_status' => 1,
                        'mnu_parent_menu_id' => $parent_id,
                        'mnu_level <='=>2,
                        'mnu_type' => $menuType
                        ));
                $this->db->order_by('mnu_sequence','asc');
                $result = $this->db->get('menumst');
                if($result->num_rows()){
                    $resultMenu = $result->result_array();
                    foreach($resultMenu as $ele){
                        $child_parent_id = $ele['mnu_menuid'];
                        if($child_parent_id)
                        {
                            $childArr = $this->getDropdownData($child_parent_id);
                            if($childArr){
                                $ele['child'] = $childArr;
                            }
                            array_push($menuList,$ele);
                        }
                    }
                }
                return $menuList;
            }

            public function getMenuLevel($mnu_parent_id = 0){

                $mnu_level = 1;
                if($mnu_parent_id)
                {
                    $this->db->select('mnu_level');
                    $this->db->where('mnu_menuid',$mnu_parent_id);
                    $result = $this->db->get('menumst');
                    if($result->num_rows())
                    {
                        $mnu_level = $result->row()->mnu_level;
                        $mnu_level++;
                    }
                }
                return $mnu_level;
            }
            
            public function getParentMenu()
            {
                $this->db->select('mnu_menuid,mnu_menu_name,mnu_level');
                $this->db->where('mnu_status',1);
                $this->db->order_by('mnu_parent_menu_id');
                $result = $this->db->get('menumst');
                if($result->num_rows()){
                    return $result->result();
                }
                else
                    return 0;

            }
            
            public function getApplicationSetting($app_key = ''){
                if(!empty($app_key)){
                    $this->db->where('app_key',$app_key);
                    $result = $this->db->get('application_settings');
                    if($result->num_rows()){
                        return $result->row();    
                    }
                    else
                        return '';
                }
                else
                    return '';
            }
            
            public function action($action,$arrData = array(),$edit_id =0)
            {
                switch($action){
                    case 'insert':
                    $this->db->insert('menumst',$arrData);
                    return $this->db->insert_id();
                    break;
                    case 'update':
                    $this->db->where('mnu_menuid',$edit_id);
                    $this->db->update('menumst',$arrData);
                    return $edit_id;
                    break;
                    case 'insert_update_content':
                    $menu_id = $arrData['cont_menuid'];
                    $cont_content_id = 0;
                    $this->db->select('cont_contentid');
                    $this->db->where('cont_menuid',$menu_id);
                    $resultData = $this->db->get('contentmst');
                    if($resultData->num_rows())
                        $cont_content_id = $resultData->row()->cont_contentid;
                    if($cont_content_id){
                        $this->db->where('cont_contentid',$cont_content_id);
                        $this->db->update('contentmst',$arrData);
                        return $cont_content_id;
                    }else
                    {
                        $this->db->insert('contentmst',$arrData);
                        return $this->db->insert_id();
                    }
                    return $edit_id;
                    break;
                    case 'delete':
                    break;
                }
            }
            
            public function getNewSequence($mnu_parent_id,$mnu_type)
            {
                $this->db->select_max('mnu_sequence');
                $this->db->where('mnu_parent_menu_id',$mnu_parent_id);
                $this->db->where('mnu_type',$mnu_type);
                $result = $this->db->get('menumst');
                if($result->num_rows() > 0)
                {
                    $curr_seq = $result->row()->mnu_sequence;
                    if(!empty($curr_seq))
                        return ($curr_seq + 1);
                    else
                        return 1;
                }
                else
                {
                    return 1;
                }
            }
            
            public function change_sequence($mnu_id = 0,$change_to = 'up')
            {
                // get sequence of current menu
                $curr_menu = 0;
                $this->db->select('mnu_menuid,mnu_sequence,mnu_parent_menu_id,mnu_type');
                $this->db->where('mnu_menuid',$mnu_id);
                $result = $this->db->get('menumst');
                if($result->num_rows() > 0)
                {
                    $curr_menu = $result->row();
                }
                
                
                $other_menu = 0;
                $this->db->select('mnu_menuid,mnu_sequence,mnu_parent_menu_id,mnu_type');
                if($change_to == 'up')
                {
                    $this->db->where('mnu_sequence <',$curr_menu->mnu_sequence);
                    $this->db->order_by('mnu_sequence','DESC');
                }
                else{
                    $this->db->where('mnu_sequence >',$curr_menu->mnu_sequence);
                    $this->db->order_by('mnu_sequence','ASC');
                }
//                    $this->db->where('is_deleted',0);
                $this->db->where('mnu_parent_menu_id',$curr_menu->mnu_parent_menu_id);
                $this->db->where('mnu_type',$curr_menu->mnu_type);
                $this->db->limit(1);

                $result = $this->db->get('menumst');
                if($result->num_rows() > 0)
                {
                    $other_menu = $result->row();
                }
                else
                    return 'NA';

                if($other_menu){
                        // update sequence of current menu
                    $update_seq = ($other_menu->mnu_sequence);
                    $update_data = array('mnu_sequence'=>$update_seq);
                    $this->db->where('mnu_menuid',$curr_menu->mnu_menuid);
                    $this->db->update('menumst',$update_data);

                        // update sequence of other menu
                    $update_seq = ($curr_menu->mnu_sequence);
                    $update_data = array('mnu_sequence'=>$update_seq);
                    $this->db->where('mnu_menuid',$other_menu->mnu_menuid);
                    $this->db->update('menumst',$update_data);

                    return 'DONE';
                }

            }
            
            public function getContentData($menu_id = 0){
                $this->db->where('menumst.mnu_menuid',$menu_id);
//                $this->db->where('menumst.is_deleted',0);
                $this->db->from('menumst');
                $this->db->join('contentmst cont','menumst.mnu_menuid = cont.cont_menuid','left');
                $resultData = $this->db->get();
                if($resultData->num_rows())
                {
                    return $resultData->row();
                }
                else
                    return 0;
            }
            public function update_menu_status($update_array,$ele_all_child)
            {
                $this->db->where('mnu_menuid in ('.$ele_all_child.')');
                $this->db->update('menumst',$update_array);
                //echo $this->db->last_query();
                return 1;
            }


            public function update_level($diff_in_level,$all_child_ids)
            {
                $result = $this->db->query("update aoe_menumst set mnu_level = (mnu_level + ".$diff_in_level.") WHERE mnu_menuid in (".$all_child_ids.")");
//                echo $this->db->last_query();die;
            }
            
            public function check_url_exists($link,$id = FALSE)
            {
                if($id === FALSE)
                {
                    $this->db->select('cont_url_name');
                    $this->db->from('contentmst');
                    $this->db->where('cont_url_name',urlencode($link));
                    $this->db->limit(1);
                    $query = $this->db->get();
                    if($query->num_rows() > 0)
                        return false;
                    else
                        return true;
                }
                else
                {
                    $this->db->select('cont_url_name');
                    $this->db->from('contentmst');
                    $this->db->where('cont_url_name',urlencode($link));
                    $this->db->where('cont_menuid <> ',$id);
                    $this->db->limit(1);
                    $query = $this->db->get();
                    if($query->num_rows() > 0)
                        return false;
                    else
                        return true;
                }
            }
            
            public function delete_heder_image($id){
               $result = $this->db->query("update aoe_contentmst set cont_header_img = '' WHERE cont_contentid=".$id);
               return $result;
           }

       }
       ?>