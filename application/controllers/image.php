<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Image extends MY_Controller { 
	
	public function __construct() {
		parent::__construct();
		$this->load->model('image_model');
	}

	public function upload_marker_image() {
		$status = "";
		$msg = "";
		$file_id = "";

		if( !$this->is_user_logged_in ) {
			$status = "error";
			$msg = xlang('dist_errsess_expire');
			echo json_encode(array('status' => $status, 'msg' => utf8_encode($msg), "file_id" => $file_id) );
			return;
		}

		$file_element_name = 'userfile';

		$config['upload_path'] = $this->dist['upload']['path'];
		$config['allowed_types'] = implode("|",$this->dist['image_settings']['allowed_types']);
		$config['max_size']  = $this->dist['upload']['max_size'];
		$config['encrypt_name'] = TRUE;
		
		$this->load->library('upload', $config);
		
		$min_image_size = $this->dist['image_settings']['min_image_size'];

		if ( !$this->upload->do_upload( $file_element_name ) ) 	{
			$status = "error";
			$msg =  utf8_encode( $this->upload->display_errors('','') );
		} else {
			$data = $this->upload->data();

			if( $data['image_height']< $min_image_size || $data['image_width']< $min_image_size ) {
				$status="error";
				$msg = xlang('dist_min_image_size', $min_image_size);
			} else {
				$thumbSizes = $this->input->post('thumbs');
				if( !empty($thumbSizes) ) {
					$thumbSizes = explode("|", $thumbSizes );
				}

				$marker_id = $this->input->post('marker_id');
				$comment_id = $this->input->post('$comment_id');
				$user_id = $this->login_data['user_id'];
				$title = $this->input->post('title');
				$image_data = array('marker_id'=>$marker_id,
									'comment_id'=>$comment_id,
									'user_id'=>$user_id,
									'title'=> $title);

				$file_id = $this->image_model->insert( $data, $image_data, $thumbSizes );

				if( $file_id ) {
					$status = "success";
					$msg = xlang('dist_imgupload_ok');
				} else {
					@unlink( $data['full_path'] );
					$status = "error";
					$msg = xlang('dist_imgupload_nok');
				}
			}
		}

		$msg = utf8_encode($msg);
		echo json_encode(array('status' => $status, 'msg' => $msg, "file_id" => $file_id) );
	} // upload_marker_imagem

	public function upload_avatar() {

		if( !$this->is_user_logged_in ) {
			$status = "error";
			$msg = xlang('dist_errsess_expire');
			echo json_encode( array('status' => $status, 'msg' => utf8_encode($msg)) );
			return;
		}

		$this->load->model('user_model');

		$status = "";
		$msg = "";
		$img_src = "";

		$file_element_name = 'userfile';

		$upload_path = $this->dist['upload']['path'];

		$config['upload_path'] = $upload_path;
		$config['allowed_types'] = implode("|",$this->dist['image_settings']['allowed_types']);
		$config['max_size']  = $this->dist['upload']['max_size'];
		$config['encrypt_name'] = TRUE;
		
		$this->load->library('upload', $config);
		
		$min_image_size = $this->dist['image_settings']['min_image_size'];

		if ( !$this->upload->do_upload( $file_element_name ) ) 	{
			$status = "error";
			$msg = $this->upload->display_errors('','');
		} else {
			$upload_data = $this->upload->data();

			if( $upload_data['image_height']< $min_image_size ||
					$upload_data['image_width']< $min_image_size ) {
				$status="error";
				$msg = xlang('dist_min_image_size', $min_image_size);
			} else {
				$thumbSizes = $this->input->post('thumbs');
				if( !empty($thumbSizes) ) {
					$thumbSizes = explode("|", $thumbSizes );
				}

				$user_id = $this->login_data['user_id'];

				if( $this->user_model->update_avatar( $upload_data, $user_id, $thumbSizes ) ) {
					$status = "success";
					$msg = xlang('dist_imgupload_ok');
					$img_src = base_url().$upload_path.thumb_filename($upload_data['file_name'], 200);
				} else {
					@unlink( $data['full_path'] );
					$status = "error";
					$msg = xlang('dist_imgupload_nok');
				}
			}
		}

		$msg = utf8_encode($msg);
		echo json_encode(array('status' => $status, 'msg' => $msg, "img_src" => $img_src) );
	} // upload_marker_imagem

	public function list_marker_images( $marker_id ) {
		//$this->load->helper('image_helper');
		$files = $this->image_model->get_marker_images($marker_id);
		$this->load->view('marker_images', array('files' => $files));
	}

	public function delete_image( $id ) {
		if( !$this->is_user_logged_in ) {
			$status = "error";
			$msg = xlang('dist_errsess_expire');
			echo json_encode( array('status' => $status, 'msg' => utf8_encode($msg)) );
			return;
		}

		$status = $msg = "";
		
		if( !$this->image_model->delete( $id ) ) {
 			$status = 'error';
      		$msg = xlang('dist_imgdel_nok');
		} else {
			$status = "success";
			$msg = xlang('dist_imgdel_ok');
		}
		$msg = utf8_encode( $msg );
		echo json_encode( array('status' => $status, 'msg' => utf8_encode($msg)) );
	}

	public function get_image($image_id) {
		$image_data = $this->image_model->get_by_id($image_id);
		$this->load->view("marker_image_single", array("image"=>$image_data));
	}

} // Image class
