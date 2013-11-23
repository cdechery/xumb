<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload extends MY_Controller { 
	
	public function __construct() {
		parent::__construct();
		$this->load->model('image_model');
	}

	public function index() {
		$this->load->view('upload_view');
	}

	public function upload_image() {
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
	} // upload_imagem

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
	} // upload_imagem

	public function list_images($marker_id, $thumb_size = 0) {
		$files = $this->image_model->get_files($marker_id, $thumb_size);
		$this->load->view('imagens', array('files' => $files));
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
      		$msg = xlang('dist_imgdel_ok');
		} else {
			$status = "success";
			$msg = xlang('dist_imgdel_nok');
		}
		$msg = utf8_encode( $msg );
		echo json_encode( array('status' => $status, 'msg' => utf8_encode($msg)) );
	}
} // Upload class
