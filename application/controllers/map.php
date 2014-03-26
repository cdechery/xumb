<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Map extends MY_Controller {
	
	public function __construct() {
		parent::__construct();
		
		$this->load->model('map_model');
		$this->load->model('image_model');
		$this->load->config('custom_marker');
		$this->load->helper('xlogin');

		$this->logged_user_id = $this->login_data['user_id'];
	}

	public function index() {
		$this->load->library('googlemaps');

		$config = array();
		$config['center'] = $this->dist['googlemaps']['center'];
		$config['zoom'] = $this->dist['googlemaps']['zoom'];	
		$config['geocodeCaching'] = $this->dist['googlemaps']['geocodeCaching'];
		$config['minifyJS'] = $this->dist['googlemaps']['minifyJS'];
		$config['places'] = $this->dist['googlemaps']['places'];
		$config['cluster'] = $this->dist['googlemaps']['cluster'];
		$config['clusterGridSize'] = $this->dist['googlemaps']['clusterGridSize'];
		$config['sensor'] = $this->dist['googlemaps']['sensor'];
		
		$custom_js_global = "";
		$custom_js_init = "";

		$custom_js_global = "var markers_owned = new Array();";
		$custom_js_global .= "var markers_not_owned = new Array();";

		if( $this->is_user_logged_in ) {
			$config['ondblclick'] = 'createMarker({ map: map, position:event.latLng, draggable: true }, true);';
		}

		$data['marcacoes'] = $this->map_model->get_markers();

		if( $this->dist['use_categories'] ) {
			$categories = $this->map_model->get_categories();

			foreach ($categories as $id => $cat) {
				$custom_js_global .= "var cat_".$id."_markers = new Array();\n";
			}
		}
		
		foreach($data['marcacoes'] as $dbMrk) {
			$marker = array();
			$marker['position'] = $dbMrk['latitude'].', '.$dbMrk['longitude'];
			$marker['infowindow_content'] = $dbMrk['name'];
			$marker['clickable'] = true;
			$marker['id'] = $dbMrk['id'];

			if( $this->dist['use_categories'] && 
				$dbMrk['category_id'] != $this->dist['default_category'] ) {
 				$marker['icon'] = base_url() . "icons/".$categories[ $dbMrk['category_id'] ]['icon'];
				$custom_js_init .= "cat_".$dbMrk['category_id']."_markers.push(marker_".$marker['id'].");\n";
 			}
			
			if( $dbMrk['user_id'] == $this->logged_user_id ) {
				$custom_js_init .= "markers_owned.push( marker_".$marker['id']." );";
			} else {
				$custom_js_init .= "markers_not_owned.push( marker_".$marker['id']." );";
			}

			$this->googlemaps->add_marker($marker);
		}

		$config['custom_js_global'] = $custom_js_global;
		$config['custom_js_init'] = $custom_js_init;

		$this->googlemaps->initialize($config);
		
		$data['map'] = $this->googlemaps->create_map();

		if( $this->dist['use_categories'] ) {
			$data['categories'] = $categories;
		}
		
		$head_data = array("title"=>$this->dist['site_title'].": Home");
		$this->load->view('head', $head_data);
		$this->load->view('map', $data);
		$this->load->view('foot');
	}

	// this method can be used to have Xumb work with 
	// a single user, and show and manipulate its markers
	private function _set_fixed_user( $userid ) {
		if( !$this->is_user_logged_in ) {
			$this->load->helper('xlogin');
			return $this->xlogin->set_user_session( $userid );
		} else {
			return true;
		}
	}

	public function marker_infowindow($marker_id) {
		$data = $this->map_model->get_marker( $marker_id );
		$images = $this->image_model->get_marker_images($marker_id);
		$data['images'] = $images;
		if( $this->dist['use_categories'] ) {
			$data['category'] = $this->map_model->get_category( $data['category_id'] );
		}
		
		$this->load->view("marker_infowindow", array("data" => $data) );
	}
	
	public function newmarker_infowindow($lat, $long) {
		$data = array("lat" => $lat, "long" => $long);

		if( $this->dist['use_categories'] ) {
			$categories = $this->map_model->get_categories();
			$data['categories'] = $categories;
		} else {
			$data['default_category'] = $this->dist['default_category'];
		}

		$this->load->view("newmarker_infowindow", $data);
	}

	public function show_marker( $id ) {
		$marker_data = $this->map_model->get_marker( $id );

		if( $this->dist['use_categories'] ) {
			$category = $this->map_model->get_category( $marker_data['category_id'] );
			$marker_data['category'] = $category;
		}

		$this->load->view('head', array("min_template"=>"image_view", "title"=>$this->dist['site_title'].": Marcador"));
		$this->load->view('marker_view', $marker_data);
		$this->load->view('foot');
	}
	
	public function modify_marker( $id ) {

		if( !$this->check_owner( $this->map_model, $id ) ) {
			show_error( xlang('dist_errperm_marker') );
		}
		
		$marker_data = $this->map_model->get_marker($id);
		
		$head_data = array("min_template"=>"image_upload", "title"=>$this->dist['site_title'].": Marcador");
		$this->load->view('head', $head_data);

		$this->load->view('marker_form', array( 'data'=>$marker_data) );
		$this->load->view('foot');
	}
	
	public function update_marker() {

		$this->load->library('form_validation');
		$this->load->helper( array('form') );


		$status = $msg = "";
		if( !$this->is_user_logged_in ) {
			$status = "error";
			$msg = xlang('dist_errsess_expire');
		} else {
			$data = $this->input->post(NULL, TRUE);
			
			$this->form_validation->set_rules('name', xlabel('name'), 'trim|required|min_length[5]|max_length[100]|xss_clean');
			$this->form_validation->set_rules('description', xlabel('description'), 'trim|max_length[400]|xss_clean');
			
			$custfields = $this->config->item('custmark_info');
			if( count($custfields) ) {
				foreach ($custfields as $field) {
					$this->form_validation->set_rules($field['form_name'],
						$field['label'], $field['form_validation']);
				}
			}

			if ( $this->form_validation->run() == FALSE ) {
				$status = "error";
				$msg = validation_errors();
			} else {
				if( $this->map_model->update_marker( $data ) ) {
					$status = "success";
					$msg = xlang('dist_data_update_ok');
				} else {
					$status = "error";
					$msg = xlang('dist_data_update_nok');
				}
			}
		}
		
		echo json_encode(array('status' => $status, 'msg' => utf8_encode($msg)) );
	}
	
	public function new_marker() {
		$status = "error"; $msg = xlang('dist_general_error');
		$marker_id = 0;

		if( !$this->is_user_logged_in ) {
			$msg = xlang('dist_errsess_expire');
			echo json_encode( array('status' => "error", 'msg' => utf8_encode($msg)) );
			return;
		}

		$this->load->library('form_validation');
		$this->load->helper( array('form') );

		$data = $this->input->post(NULL, TRUE);
		
		if( empty($data['name']) ) {
			$this->load->library('googlemaps');
			$this->googlemaps->initialize();

			$street_address = $this->googlemaps->get_address_from_lat_long($data['latitude'], $data['longitude']);
			if( $street_address['status'] == "OK" ) {
				$data['name'] = $street_address['address']; 
			} else {
				$next_id = $this->map_model->get_next_marker_id( $this->logged_user_id );
				$data['name'] = "Marker ".$next_id;
			}
		}
		
		$data['user_id'] = $this->logged_user_id;
		$new_marker_id = $this->map_model->insert_marker( $data );
		
		if( $new_marker_id ) {
			$status = "success";
			$msg = "New marker was created";
			$marker_id = $new_marker_id;

			/*$data['id'] = $new_marker_id;
			$data['description'] = "";

			$head_data = array("min_template"=>"image_upload", "title"=>$this->dist['site_title'].": Marcador");
			$this->load->view('head', $head_data);
				
			$this->load->view('marker_form', array('data' => $data) );
			$this->load->view('foot');*/

			//redirect('/map/modify_marker/'.$new_marker_id);
		} else {
			$status = "error";
			$msg = xlang('dist_general_error');
		}

		echo json_encode( array('status' => $status, 'msg' => utf8_encode($msg), 'marker_id'=>$marker_id) );
	}	

	public function delete_marker( $marker_id ) {

		if( !$this->check_owner( $this->map_model, $marker_id ) ) {
			show_error( xlang('dist_errperm_marker') );
		}

		$status = "error"; $msg = xlang('dist_general_error');

		// I had to leave this transction code here, because I didn't want
		// a Model calling another Model's method
		$images = $this->image_model->get_marker_images( $marker_id );

		$this->db->trans_begin();

		foreach( $images as $img ) {
			$this->image_model->delete( $img->id );
		}

		$this->map_model->delete_marker( $marker_id );

		$this->db->trans_complete();

		if( $this->db->trans_status() ) {
			$status = "success";
			$msg = "O marcador foi excluído com sucesso";
		}

		echo json_encode( array('status'=>$status, 'msg'=>utf8_encode($msg)) );
	}
}
