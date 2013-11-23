<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends MY_Controller {
	
	public function __construct() {
		parent::__construct();	
	}

	public function index($msg = "") {
		$head_data = array("title"=>$this->dist['site_title'].": Login");
		$this->load->view('head', $head_data);
		$this->load->view('login', array('msg'=>$msg));
		$this->load->view('foot');
	}

	public function verify() {
		$this->load->model('user_model');
		$this->load->helper('url');

		$form_data = $this->input->post(NULL, TRUE);

		$user_data = $this->user_model->check_login( $form_data['login'], $form_data['password'] );

		if( $user_data ) {
			$session_data = array('logged_in'=>TRUE,
					  		'user_id' => $user_data['id'],
					  		'name' => $user_data['name'] );

			$this->session->set_userdata( $session_data );

			// TODO - Cookie Support

			redirect( base_url() );
		} else {
			$this->index( xlang('dist_login_failed') );
		}
	}
}

