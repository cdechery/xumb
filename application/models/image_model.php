<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Image_model extends CI_Model {
	
	private $dist = array(); //distribution settings

	public function __construct() 	{
		parent::__construct();
		$this->load->helper('image');

		// load distribution configuration
		$this->dist = $this->config->item('dist');
	}
 
	public function insert($upload_data, $image_data, $thumb_sizes = array() )	{
		
		if( empty($image_data["marker_id"]) ) {
			$image_data["marker_id"] = "0";
		}
		
		if( empty($image_data["comment_id"]) ) {
			$image_data["comment_id"] = "0";
		}
		
		$insert_data = array(
			'marker_id' => (int)$image_data["marker_id"],
			'comment_id' => (int)$image_data["comment_id"],
			'user_id' => (int)$image_data["user_id"],
			'filename'     => $upload_data['file_name'],
			'description'  => $image_data["title"]
		);
		
		if( $this->db->insert('image', $insert_data) ) {
			if( count($thumb_sizes) ) {
				foreach( $thumb_sizes as $size ) {
					create_square_cropped_thumb( $upload_data['full_path'], $size );
				}
			}
			return $this->db->insert_id();
		} else {
			return false;
		}
	}
	
	public function get_images($marker_id) {
		$images =  $this->db->get_where('image', array('marker_id'=>$marker_id))->result();
		return $images;
	}
	
	public function delete($id = 0) {
		if( $id === 0 ) {
			return false;
		} else {
			$thumb_sizes = $this->dist['image_settings']['thumb_sizes'];
			$path = $this->dist['upload']['path'];
			
			$image = $this->get_by_id( $id );
			$filename = "";
			if( !$image ) {
				return false;
			} else {
				$filename = $image->filename;
			}
			
			$this->db->delete('image', array('id' => $id) );
			
			if( $this->db->affected_rows() ) {
				@unlink( $path . $filename );
				foreach( $thumb_sizes as $size ) {
					@unlink( $path . thumb_filename($filename, $size ) ); 
				}
				return true;
			} else {
				return false;
			}
		}
	}
	
	public function get_by_id($id = 0) {
		if( $id === 0 ) {
			return false;
		} else {
			return $this->db->get_where('image', array('id' => $id))->row();
		}
	}	
}