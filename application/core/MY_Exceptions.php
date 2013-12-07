<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Exceptions extends CI_Exceptions {

	private $CI;
	private $dist;

	public function __construct() {
		parent::__construct();
		$this->CI =& get_instance();
		$this->dist = $this->CI->config->item('dist');
	}

	function show_error($heading, $message, $template = 'error_general', $status_code = 500) {

		set_status_header($status_code);

		if( $heading==NULL ) {
			$heading = $this->CI->lang->language["dist_error_head"];
		}

		$head_data = array("title"=>$this->dist['site_title'].": ".$this->CI->lang->language["dist_lbl_error"]);
		$head = $this->CI->load->view('head', $head_data, TRUE);

		$err_body = $this->CI->load->view('error', array('message'=>$message, 'heading'=>$heading), TRUE);
		$foot = $this->CI->load->view('foot', TRUE);
		
		if (ob_get_level() > $this->ob_level + 1) {
			ob_end_flush();
		}
		ob_start();

		echo $head;
		echo $err_body;
		echo $foot;

		$buffer = ob_get_contents();
		
		ob_end_clean();
		
		return $buffer;
	}
}
?>