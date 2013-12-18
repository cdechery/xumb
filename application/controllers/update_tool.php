<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
	class Update_tool extends CI_Controller {

		var $dist;
		var $base_skip_files = array(
			'/.htaccess',
			'/index.php', 
			'/application/config/config.php',
			'/application/config/database.php',
			'/application/config/dist.php');

		public function __construct() {
			parent::__construct();
			$this->dist = $this->config->item('dist');
		}

		public function index($msg = NULL) {
			$this->session->sess_destroy();
			$this->load->view('update_tool', array('msg'=>$msg));
		}

		public function confirm_settings() {
			$pwd = $this->input->post('password');

			if( empty($pwd) ) {
				$this->index("No password was provided. Try again!");
				return;
			}

			if( $pwd != $this->dist['update_tool']['password'] ) {
				$this->index("Incorrect password, try again!");
				return;
			}

			// all good, set a session to the following steps 
			$session_data = array('update_auth'=>1);
			$this->session->set_userdata( $session_data );

			$settings = "";

			$settings = "Custom files to skip: ";
			if( $this->dist['update_tool']['skip_files']=='' ) {
				$settings .= "NONE";
			} else {
				$settings .= "<ul><li>";
				$settings .= implode( "</li><li>", $this->dist['update_tool']['skip_files'] );
				$settings .= "</li></ul>";
			}

			$this->load->view('update_tool', array('step'=>'confirm', 'settings'=>$settings) );
		}

		public function do_update() {
			$sess = $this->session->all_userdata();
			if( !isset($sess['update_auth']) ) {
				$this->index('Session is invalid or expired!');
				return;
			}

			$dist = $this->config->item('dist');
			$base = dirname($_SERVER['SCRIPT_FILENAME']);
			$full_path = $base."/update/master.zip";

			if( $this->dist['update_tool']['skip_files']!=''
				 && count($this->dist['update_tool']['skip_files']) ) {
				$this->base_skip_files = array_merge($this->base_skip_files, $this->dist['update_tool']['skip_files']);
			}

			array_walk($this->base_skip_files, 'prefix', $base);

			$output = "Checking for master file existence: ";
			if( !@fopen($full_path, "r") ) {
				$output .= "File <i>master.zip</i> not found on update dir.";
			} else {
				$output .= "OK<br>";
				$output .= "Unzipping master file: ";
				$zip = new ZipArchive;
				$res = $zip->open( $full_path );
				if ($res === TRUE) {
					// extract it to the path we determined above
					$zip->extractTo($base."/update");
					$zip->close();
					$output .= "OK<br>";

					$output .= "Copying files: ";
					$this->recurse_copy($base."/update/xumb-master", $base);
					$output .= "OK<br>";

					$output .= "Checking for changes on <i>dist.php</i>: ";

					include($base."/application/config/dist.php");
					$old_dist = $config['dist'];
					include($base."/update/xumb-master/application/config/dist.php");
					$new_dist = $config['dist'];

					$dif = array_diff_key($old_dist, $new_dist);
					if( count($dif) ) {
						$output .= "<u>There are changes (new stuff) on <i>dist.php</i></u>. You must merge the new and current versions manually.";
					} else {
						$output .= "No changes. You're good!";
					}

					$output .= "<br><br><b>Update finished!</b>";
				} else {
					$output .= "There was a failure unzipping, check your log files";
				}
			}

			$this->load->view('update_tool', array('step'=>'do_update', 'output'=>$output) );
		}

		private function recurse_copy($src,$dst) { 
		    $dir = opendir($src); 
		    @mkdir($dst); 
		    while(false !== ( $file = readdir($dir)) ) { 
		        if (( $file != '.' ) && ( $file != '..' )) { 
		            if ( is_dir($src . '/' . $file) ) { 
		                $this->recurse_copy($src . '/' . $file,$dst . '/' . $file); 
		            } else { 
		            	$dest_file = $dst."/".$file;
		            	if( !in_array($dest_file, $this->base_skip_files) && 
		            		strpos($dest_file, '/views/')===FALSE ) {
			                copy($src . '/' . $file,$dst . '/' . $file); 
		            	}
		            } 
		        } 
		    } 
		    closedir($dir); 
		} 	

	} // class

	function prefix(&$file, $key, $prefix) {
		$file = $prefix.$file;
	}
?>
