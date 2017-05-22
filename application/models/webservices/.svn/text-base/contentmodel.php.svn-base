<?php
/*
  Model that contains function related to CMS content
 */
class ContentModel extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  /*
   * Method Name: get_content
   * Purpose: To get content for a page from database
   * params:
   *      input: page_title
   *      output: content
   */
  public function get_content( $page_title )
  {
    $this->db->select("c.cont_content as content");
    $this->db->from("contentmst c");
//    $this->db->join("menumst m","m.mnu_menuid = c.cont_menuid");
//    $data = array(
//              'm.mnu_menu_name' => $page_title
//            );
      $data = array(
          'c.cont_page_title' => $page_title
      );
    $this->db->where( $data );
    $this->db->limit(1);
    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? $query->row()->content : '';
  }

    /*
     * Author: Akshay Deshmukh
     * Method Name: get_content_page_title
     * Purpose: To get content page title for a page from database
     * params:
     *      input:
     *      output: page title
     */
    public function get_content_page_title( )
    {
        $this->db->select("c.cont_page_title as title, m.mnu_menu_name as menu_name");
        $this->db->from("contentmst c");
        $this->db->join("menumst m","m.mnu_menuid = c.cont_menuid");
//    $this->db->join("menumst m","m.mnu_menuid = c.cont_menuid");
//    $data = array(
//              'm.mnu_menu_name' => $page_title
//            );
        $query = $this->db->get();
        return ( $query->num_rows() > 0 ) ? $query->result_array() : '';
    }

  /*
   * Method Name: get_faq
   * Purpose: To get faq content for a page from database
   * params:
   *      input: -
   *      output: ques, ans array
   */
  public function get_faq()
  {
    $this->db->select("faq_question as question, faq_answer as answer");
    $this->db->from("hzi_faq");
    $data = array(
              'is_delete' => '0',
              'status' => '1'
            );
    $this->db->where( $data );
    $query = $this->db->get();

    return ( $query->num_rows() > 0 ) ? $query->result() : array();
  }

}
?>