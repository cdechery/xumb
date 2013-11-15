<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<?php
class Map_model extends MY_Model {
	
	public function __construct() {
		parent::__construct();
		$this->table = 'marker';
	}
	
	public function insert_marker( $data ) {
		
		// insert from the map only requires de name, lat and long
		$insert_array = array( 'name' => $data['name'],
							  'latitude' => $data['latitude'],
							  'longitude' => $data['longitude'],
							  'user_id' => $data['user_id'],
							  'category_id' => $data['cat']);
		
		$this->db->set('creation_date', 'NOW()', false);
		$this->db->insert('marker', $insert_array );
		//$insert_string = $this->db->insert_string('marker', $insert_array );
		
		//$this->db->query( $insert_string );
		
		return $this->db->insert_id();
	}
	
	public function update_marker( $data ) {

		$update = array( 'name' => $data['name'],
						'description' => $data['description'],
						'latitude' => $data['latitude'],
						'longitude' => $data['longitude'] );
		
		return( $this->db->update('marker', $update, array('id' => $data['id'])) );	
	}
	
	public function get_category( $id = NULL ) {
		if( $id === NULL ) {
			return false;
		} else {
			return $this->db->get_where('category', array('id' => $id))->row_array();
		}
	}

	public function get_marker( $id = NULL ) {
		if( $id === NULL ) {
			return false;
		} else {
			return $this->db->get_where('marker', array('id' => $id))->row_array();
		}
	}

	public function get_categories() {
			$cat_array = array();
			$ret = $this->db->get_where('category', array('id !='=> $this->dist['default_category']) );
			
			foreach ($ret->result() as $cat) {
				$cat_array[ $cat->id ] = array('name'=>$cat->name,
											   'description'=>$cat->description,
											   'icon'=>$cat->icon);
			}
			return $cat_array;
	}

	public function get_markers($userid = FALSE) {
		if ($userid === FALSE) {
			$query = $this->db->get('marker');
			return $query->result_array();
		}
		
		$query = $this->db->get_where('marker', array('user_id' => $userid) );
		return $query->row_array();
	}
	
	public function get_next_marker_id( $user_id ) {
		$query = $this->db->get_where('marker',array('user_id'=>$user_id) );
		if( count($query) && $query->num_rows()>0 ) {
			return $query->num_rows()+1;
		} else {
			return "1";
		}
	}
}
