<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends MY_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->load->model('user_model');
	}
	
	public function logout() {
		$this->session->sess_destroy();
		redirect( base_url() );
	}


	public function new_user() {
		if( $this->is_user_logged_in ) {
			redirect( base_url() );
		}

		$head_data = array("title"=>$this->dist['site_title']);
		$this->load->view('head', $head_data);

		$data = array('action' => 'insert');
		$this->load->view('user_form', array('data'=>$data) );
		$this->load->view('foot');
	}

	public function insert() {
		$status = "";
		$msg = "";
		$new_id = 0;

		$user_data = $this->input->post(NULL, TRUE);

		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');

		$this->form_validation->set_error_delimiters('','</br>');

		$this->form_validation->set_rules('login', xlabel('username'), 'required|min_length[5]|max_length[12]|is_unique[user.login]|xss_clean');
		$this->form_validation->set_rules('name', xlabel('name'), 'required|min_length[5]|max_length[50]');
		$this->form_validation->set_rules('surname', xlabel('surname'), 'required|min_length[5]|max_length[50]');
		$this->form_validation->set_rules('email', xlabel('email'), 'required|is_unique[user.email]|valid_email');
		$this->form_validation->set_rules('password', xlabel('password'), 'required');
		$this->form_validation->set_rules('password_2', xlabel('passconf'), 'required|matches[password]');
		$this->form_validation->set_rules('zipcode', xlabel('zip'), 'numeric');

		if ($this->form_validation->run() == FALSE) {
			$status = "ERROR";
			$msg = validation_errors();
		} else {
			$new_id = $this->user_model->insert( $user_data );

			if( $new_id > 0 ) {
				$status = "OK";
				$msg = xlang('dist_newuser_ok');
			} else {
				$status = "ERROR";
				$msg = xlang('dist_newuser_nok');
			}
		}

		echo json_encode( array('status'=>$status, 'msg'=>utf8_encode($msg)) );
	}

	public function modify() {
		if( !$this->is_user_logged_in ) {
			show_error( xlang('dist_errsess_expire') );
		}

		$this->load->helper('image_helper');

		$head_data = array("min_template"=>"image_upload", "title"=>$this->dist['site_title']);
		$this->load->view('head', $head_data);

		$user_data = $this->user_model->get_data( $this->login_data['user_id'] );
		$user_data['action'] = 'update';


		if( !empty($user_data['avatar']) ) {
			$user_data['avatar'] = thumb_filename($user_data['avatar'], 200);
		}

		$this->load->view('user_form', array('data'=>$user_data) );
		$this->load->view('foot');
	}

	public function update() {
		$status = "";
		$msg = "";

		if( !$this->is_user_logged_in ) {
			$status = "error";
			$msg = xlang('dist_errsess_expire');
		} else {
			$user_data = $this->input->post(NULL, TRUE);

			$this->load->helper(array('form', 'url'));
			$this->load->library('form_validation');

			$this->form_validation->set_error_delimiters('','</br>');

			$this->form_validation->set_rules('name', 'Nome', 'required|min_length[5]|max_length[50]');
			$this->form_validation->set_rules('surname', 'Sobrenome', 'required|min_length[5]|max_length[50]');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_email_check');
			$this->form_validation->set_rules('password', 'Senha', '');
			$this->form_validation->set_rules('password_2', 'Confirmação de Senha', 'matches[password]');
			$this->form_validation->set_rules('zipcode', 'CEP', 'numeric');

			if ($this->form_validation->run() == FALSE) {
				$status = "ERROR";
				$msg = validation_errors();
			} else {
				$ret_update = $this->user_model->update( $user_data, $this->login_data['user_id'] );

				if( $ret_update ) {
					$status = "OK";
					$msg = xlang('dist_upduser_ok');
				} else {
					$status = "ERROR";
					$msg = xlang('dist_upduser_nok');
				}
			}
		}

		echo json_encode( array('status'=>$status, 'msg'=>utf8_encode($msg)) );
	}

	public function email_check($email) {
		if( $this->user_model->email_exists($email, $this->login_data['user_id']) ) {
			$this->form_validation->set_message('email_check', xlang('dist_upduser_email') );
			return FALSE;
		} else {
			return TRUE;
		}
	}
}

?>