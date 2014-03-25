<?php
/*
 * Custom Xumb helper to provide session manipulation and login
 * functionality across Xumb.
 * Remember to call this from a Controller before any output
 * or call to any viewers
 */
public function set_user_session( $user_id ) {
	$CI = & get_instance();
	$CI->load->model('user_model');
	$user_data = $CI->user_model->get_data( $user_id );

	if( FALSE!=$user_data ) {
		$session_data = array('logged_in'=>TRUE,
		  	'user_id' => $user_data['id'],
		  	'name' => $user_data['name'] );

		$CI->session->set_userdata( $session_data );
		return true; 
	} else {
		return false;
	}
}