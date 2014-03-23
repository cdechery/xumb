<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends MY_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->load->model('user_model');
		$this->load->config('custom_user');
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

	public function reset_password() {
		$action = $this->input->post('action', TRUE);
		$msg = ""; $status = "form";

		if( empty($action) ) {
			$action = "do_reset";
			$msg = xlang('dist_resetpw_email');
		} else {
			$email = $this->input->post('email', TRUE);

			if( !$this->user_model->email_exists($email) ) {
				$action = "form";
				$status = "error";
				$msg = xlang('dist_resetpw_email_nok');
			} else {
				// let's generate a new password
				// not a very tricky one, but feel free to improve this
				$pwd_len = "8";
				$letters = "abcdefghijklmnopqrstuvwxyz";
				$numbers = "1234567890";

				$letters_len = strlen($letters);
				$numbers_len = strlen($numbers);

				$new_pwd = "";
				for($i=0; $i<$pwd_len-1; $i++) {
					if( $i%2==0 ) {
						$idx = rand(0,$letters_len-1);
						$new_pwd .= $letters[$idx];
					} else {
						$idx = rand(0,$numbers_len-1);
						$new_pwd .= $numbers[$idx];
					}
				}

				if( $this->user_model->update_password($email, $new_pwd) ) {
					$status = "success";
					$msg = xlang('dist_resetpw_email_ok');
					$action = "success";

					$this->send_pwd_email($email, $new_pwd);
				} else {
					$status = "error";
					$msg = xlang('dist_resetpw_email_err');
					$action = "form";
				}
			}
		}

		$view_params = array('action'=>$action, 'msg'=>$msg, 'status'=>$status);
		$this->load->view('reset_password', $view_params);
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

			$this->form_validation->set_rules('name', xlabel('name'), 'required|min_length[5]|max_length[50]');
			$this->form_validation->set_rules('surname', xlabel('surname'), 'required|min_length[5]|max_length[50]');
			$this->form_validation->set_rules('email', xlabel('email'), 'required|valid_email|callback_email_check');
			$this->form_validation->set_rules('password', xlabel('password'), 'required');
			$this->form_validation->set_rules('password_2', xlabel('passconf'), 'required|matches[password]');

			$custfields = $this->config->item('custuser_info');
			if( count($custfields) ) {
				foreach ($custfields as $field) {
					$this->form_validation->set_rules($field['form_name'],
						$field['label'], $field['form_validation']);
				}
			}

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

	public function email_check( $email ) {
		if( $this->user_model->email_exists($email, $this->login_data['user_id']) ) {
			$this->form_validation->set_message('email_check', xlang('dist_upduser_email') );
			return FALSE;
		} else {
			return TRUE;
		}
	}

	private function send_pwd_email($email, $password) {
		$this->load->library('email');

		$this->email->from($this->dist['email']['from'], $this->dist['email']['name']);
		$this->email->to( $email ); 

		$this->email->subject('Your New Password');
		$message = 'You requested a password reset at the '.$this->dist['site_title'].' website.

					Here it is: '.$password.'
					
					We suggest you login right now, change your password and 
					then delete this email. You can always use this feature later in
					the future in case you forget it again. ;)';

		$this->email->message( $message );	

		$this->email->send();		
	}
}
?>
