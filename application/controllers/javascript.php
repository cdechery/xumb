<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Javascript extends CI_Controller {
	
	public function __construct() {
		parent::__construct();	
		$this->output->set_header('Content-type: application/javascript; charset='.$this->config->item('charset'));
	}

	public function index() {
		$lang_js = $this->lang->language;
		$dist = $this->config->item('dist');

		$output_js =  "var site_root='".base_url()."';\n";
		$output_js .= "var site_charset='".$this->config->item('charset')."';\n";
		$output_js .= "var max_images_marker=".$dist['max_images_marker'].";\n";

		$output_js .= "var lang = Array();\n";
		foreach( $lang_js as $idx => $line ) {
			$output_js .= "lang['".$idx."'] = '".addslashes($line)."';\n";
		}

		echo $output_js;
	}
}