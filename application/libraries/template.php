<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

class CI_Template {

	

	public function __construct($params = array())
	{
		// construct 
	}

	// --------------------------------------------------------------------	
	// Event Code
	// --------------------------------------------------------------------

	/**
	 * load
	 *
	 * Outputs a load give view with required header and footer template
	 *
	 * @access	public
	 * @param	string	Name of the view   
	 * @return	string
	 */
	function view($child_view_to_load = '',$data = array())
	{
                $CI = &get_instance();
                $CI->load->view('admin/header_in');
		$CI->load->view('admin/'.$child_view_to_load,$data);
                $CI->load->view('admin/footer_in');
	}
}