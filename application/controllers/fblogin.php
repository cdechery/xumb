<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Fblogin extends CI_Controller {
 
    public function __construct() {
        parent::__construct();

        parse_str( $_SERVER['QUERY_STRING'], $_REQUEST );

        $CI = & get_instance();
		$CI->config->load("facebook", TRUE);

		$config = $CI->config->item('facebook');

		$this->load->library('Facebook', $config);
    }
 
    function index() {

        extract( $_GET );

        if( isset($error) && $error=="access_denied" ) {
            echo "<script type='application/javascript'>alert('Permissoes negadas!'); window.close();</script>";
            return;
        }

        // Try to get the user's id on Facebook
        $userId = $this->facebook->getUser();

        // If user is not yet authenticated, the id will be zero
        if($userId == 0) {
            // Generate a login url
            $url = $this->facebook->getLoginUrl( array('scope'=>'email', 'display'=>'popup') );
        } else {
            // Get user's data and print it
            $user_data = $this->facebook->api('/me');
            print_r($user_data);
        }
    }

    function deauth() {
        // Try to get the user's id on Facebook
        $userId = $this->facebook->getUser();
        if($userId != 0) {
            $this->facebook->api('/me/permissions', 'DELETE');            
        }
    }
 
}
 
?>