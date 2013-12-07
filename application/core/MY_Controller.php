<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
	protected $login_data = array();
	protected $dist = array();
	protected $is_user_logged_in = FALSE;
	
	public function __construct() {

		parent::__construct();

		// This header code was necessary due to some issues with AJAX calls. It doesn't work with CI's header
		// only with PHP's native header() function for some reason
		$allowed_urls = $this->allowed_urls();
		foreach( $allowed_urls as $url ) {
			header('Access-Control-Allow-Origin: '.$url);
		}
		
		// distribution settings available to all Controllers
		$this->dist = $this->config->item('dist');
		
		$this->login_data = $this->check_session();
		$this->is_user_logged_in = $this->login_data["logged_in"];

		// load 'login_status' to the views
		$this->load->vars( array('login_data' => $this->login_data, 'dist'=>$this->dist)  );
		
		//$this->output->set_header('Content-type: text/html; charset='.$this->config->item('charset'));
	}
	
	protected function check_owner( $model, $id ) {
		if( !$this->is_user_logged_in ) {
			return FALSE;
		}
		
		return $model->is_owner( $this->login_data['user_id'], $id );
	}

	private function check_session() {
		$login_status = array('user_id' => 0, 'logged_in'=>FALSE);
		
		$session = $this->session->all_userdata();

		if( isset($session["user_id"]) && $session["user_id"]!= 0 ) {
			$login_status = array("logged_in"=>TRUE,
								  "user_id" => $session["user_id"],
								  "name" => $session["name"] );
		}
		
		return $login_status;
	}
	
	private function allowed_urls() {
		$alurls = array( rtrim( base_url(), '/') );
		$custom_urls = $this->config->item('allowed_urls');
		if( !empty($custom_urls) ) {
			$alurls = array_merge( $alurls, $this->config->item('allowed_urls') );
		}
		return $alurls;
	}
}