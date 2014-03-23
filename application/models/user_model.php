<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends MY_Model {

	public function __construct() 	{
		parent::__construct();
		$this->load->config('custom_user');
	}
	
	public function get_data($userid) {
		if(empty($userid) || $userid==0) {
			return false;
		}
		
		return $this->db->get_where('user', array('id'=>$userid))->row_array();
	}

	public function check_login($login, $password) {
		$encrypted_pwd = md5($password);

		$ret = $this->db->get_where('user', array('login'=>$login, 'password'=>$encrypted_pwd) );

		if( $ret->num_rows()>0 ) {
			return $ret->row_array();
		} else {
			return FALSE;
		}
	}

	public function email_exists($email, $except_user_id = 0) {
		$query = $this->db->get_where('user', array('email'=> $email, 'id !=' => $except_user_id ) );

		return $query->num_rows() > 0;
	}

	public function insert($user_data) {

		$insert_data = array(
			'login' => $user_data['login'],
			'name' => $user_data['name'],
			'surname' => $user_data['surname'],
			'email' => $user_data['email'],
			'city' => $user_data['city'],
			'country' => $user_data['country'],
			'zip_code' => $user_data['zip_code'],
			'password' => md5( $user_data['password'] )
		);

		$this->db->set('creation_date', 'NOW()', false);

		if( $this->db->insert('user', $insert_data ) ) {
			return $this->db->insert_id();
		} else {
			return 0;
		}
	}

	public function update($user_data, $user_id) {

		if( empty($user_id) || $user_id==0 ) {
			return false;
		}

		$upd_data = array(
			'name' => $user_data['name'],
			'surname' => $user_data['surname'],
			'email' => $user_data['email'],
		);

		$custfields = $this->config->item('custuser_info');
		if( count($custfields) ) {
			foreach ($custfields as $field) {
				$upd_data[ $field['table_column'] ] = $user_data[ $field['form_name'] ];
			}
		}

		if( !empty($user_data['password']) ) {
			$upd_data['password'] = md5($user_data['password']);
		}
		
		return( $this->db->update('user', $upd_data, array('id' => $user_id)) );
	}

	public function update_password($email, $new_pwd) {
		if( empty($email) || empty($new_pwd) ) {
			return false;
		}

		$upd_data = array( 'password'=>md5($new_pwd) );
		return( $this->db->update('user', $upd_data, array('email' => $email)) );
	}

	public function update_avatar($img_data, $user_id, $thumb_sizes = array() ) {
		if( empty($img_data) || $user_id==0 ) {
			return false;
		}

		$upd_data = array(
			'avatar' => $img_data['file_name']
		);

		$this->load->helper('image_helper');
		if( count($thumb_sizes) ) {
			foreach( $thumb_sizes as $size ) {
				create_square_cropped_thumb( $img_data['full_path'], $size );
			}
		}

		return( $this->db->update('user', $upd_data, array('id' => $user_id)) );
	}

	public function deactivate($user_id) {
		$upd_data = array('is_active' => 'N');
		return( $this->db->update('user', $upd_data, array('id' => $user_id)) );	
	}

	public function delete_graceful($user_id) {
		// TODO
		return 0;
	}

	public function delete_cascade($user_id) {
		// TODO
		return 0;
	}

	public function get_users($active_only = TRUE) {
		// TODO
		return 0;
	}
}
?>
