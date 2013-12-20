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
			if( "###changethis###"==$this->dist['update_tool']['password'] ) {
				die('The Update Tool will not work until you configure your distribution correctly.');
			}
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

			$delete = $this->input->post('delete');
			$delete = ($delete=="1");

			$dist = $this->config->item('dist');
			$base = dirname($_SERVER['SCRIPT_FILENAME']);
			$full_path = $base."/update/master.zip";

			if( $this->dist['update_tool']['skip_files']!=''
				 && count($this->dist['update_tool']['skip_files']) ) {
				$this->base_skip_files = array_merge($this->base_skip_files, $this->dist['update_tool']['skip_files']);
			}

			array_walk($this->base_skip_files, 'prefix', $base);

			$master_file_ok = FALSE;

			$output = "Checking for master file existence: ";
			if( FALSE!=@fopen($full_path, "r") ) {
				$output .="OK - skipping download<br>";
				$master_file_ok = TRUE;
			} else {
				$output .= "File <i>master.zip</i> not found on update dir.<br>Trying to download from github: ";
				if( FALSE!=download_from_github($base) ) {
					$output .="OK<br>";
					$master_file_ok = TRUE;
				} else {
					$output .="Unable to download. Try downloading manually or check your log files<br>";
				}
			}

			if( $master_file_ok ) {
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

					if( $delete ) {
						$output .="<br>Deleting files on the update folder: ";
						@unlink( $base."/update/master.zip");
						@delTree($base."/update/xumb-master");
						$output .="OK";
					}

					$output .= "<br><br><b>Update finished!</b>";
				} else {
					$output .= "There was a failure unzipping, check your log files";
				}
			} else { // master_file_ok
				$output .= "<br><b>Update aborted!</b>";
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

	function download_from_github($dest_dir) {
		$ret = FALSE;
		$master = "http://github.com/cdechery/xumb/archive/master.zip";
		$remote_data = @file_get_contents( $master );
		if( $remote_data==FALSE ) {
			return FALSE;
		} else {
			$ret = @file_put_contents( $dest_dir."/update/master.zip", $remote_data);
		}

		return ( $ret!=FALSE );
	}

	function delTree($dir) { 
		$files = array_diff(scandir($dir), array('.','..')); 
		foreach ($files as $file) { 
			(is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file"); 
		} 
		return rmdir($dir); 
	} 	
?>
