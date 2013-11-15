<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Map extends MY_Controller {
	
	public function __construct() {
		parent::__construct();
		
		$this->load->model('map_model');
		$this->load->model('image_model');
		
		$this->load->library('form_validation');
		$this->load->helper( array('form', 'url') );

		$this->logged_user_id = $this->login_data['user_id'];

	}

	public function index() {
		$head_data = array("title"=>$this->dist['site_title'].": Home");
		$this->load->view('head', $head_data);
		
		$this->load->library('googlemaps');
		
		$config = array();
		$config['center'] = 'rio de janeiro';
		$config['zoom'] = '13';		
		$config['geocodeCaching'] = TRUE;
		$config['minifyJS'] = TRUE;
		$config['places'] = TRUE;
		$config['cluster'] = TRUE;
		$config['clusterGridSize'] = 40;
		
		if( $this->is_user_logged_in ) {
			$config['ondblclick'] = 'createMarker({ map: map, position:event.latLng, draggable: true }, true);';
		}

		$data['marcacoes'] = $this->map_model->get_markers();

		if( $this->dist['use_categories'] ) {
			$categories = $this->map_model->get_categories();
		}

		if( $this->dist['use_categories'] ) {
			$custom_js_global = "";
			$custom_js_init = "";

			foreach ($categories as $id => $cat) {
				$custom_js_global .= "var cat_".$id."_markers = new Array();\n";
			}
		}
		
		foreach($data['marcacoes'] as $dbMrk) {
			$marker = array();
			$marker['position'] = $dbMrk['latitude'].', '.$dbMrk['longitude'];
			$marker['infowindow_content'] = $dbMrk['name'];
			$marker['clickable'] = true;

			if( $this->dist['use_categories'] && 
				$dbMrk['category_id'] != $this->dist['default_category'] ) {
 				$marker['icon'] = base_url() . "icons/".$categories[ $dbMrk['category_id'] ]['icon'];
 			}
			$marker['id'] = $dbMrk['id'];
			
			$this->googlemaps->add_marker($marker);
			if( $this->dist['use_categories'] && $dbMrk['category_id']!=$this->dist['default_category'] ) {
				$custom_js_init .= "cat_".$dbMrk['category_id']."_markers.push(marker_".$marker['id'].");\n";
			}
		}

		if( $this->dist['use_categories'] ) {
			$config['custom_js_global'] = $custom_js_global;
			$config['custom_js_init'] = $custom_js_init;
		}

		$this->googlemaps->initialize($config);
		
		$data['map'] = $this->googlemaps->create_map();
		if( $this->dist['use_categories'] ) {
			$data['categories'] = $categories;
		}
		
		$this->load->view('map', $data);
		$this->load->view('foot');
	}

	public function marker_infowindow($marker_id) {
		$data = $this->map_model->get_marker( $marker_id );
		$images = $this->image_model->get_images($marker_id);
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
			show_error( langp('dist_errperm_marker') );
		}
		
		$marker_data = $this->map_model->get_marker($id);
		
		$head_data = array("min_template"=>"image_upload", "title"=>$this->dist['site_title'].": Marcador");
		$this->load->view('head', $head_data);

		$this->load->view('marker_form', array( 'data'=>$marker_data) );
		$this->load->view('foot');
	}
	
	public function update_marker() {

		$status = $msg = "";
		if( !$this->is_user_logged_in ) {
			$status = "error";
			$msg = langp('dist_errsess_expire');
		} else {
			$data = $this->input->post(NULL, TRUE);
			
			$this->form_validation->set_rules('name', label('name'), 'trim|required|min_length[5]|max_length[100]|xss_clean');
			$this->form_validation->set_rules('description', label('description'), 'trim|max_length[400]|xss_clean');
			
			if ( $this->form_validation->run() == FALSE ) {
				$status = "error";
				$msg = validation_errors();
			} else {
				if( $this->map_model->update_marker( $data ) ) {
					$status = "success";
					$msg = langp('dist_data_update_ok');
				} else {
					$status = "error";
					$msg = langp('dist_data_update_nok');
				}
			}
		}
		
		echo json_encode(array('status' => $status, 'msg' => utf8_encode($msg)) );
	}
	
	public function new_marker() {
		if( !$this->is_user_logged_in ) {
			show_error( langp('dist_errsess_expire') );
			return;
		}

		$this->load->library('googlemaps');
		$this->googlemaps->initialize();

		$data = $this->input->post(NULL, TRUE);
		
		if( empty($data['name']) ) {
			$street_address = $this->googlemaps->get_address_from_lat_long($data['latitude'], $data['longitude']);
			if( $street_address['status'] == "OK" ) {
				$data['name'] = $street_address['address']; 
			} else {
				$next_id = $this->map_model->get_next_marker_id( $this->logged_user_id );
				$data['name'] = "Marcador ".$next_id;
			}
		}
		
		$data['user_id'] = $this->logged_user_id;
		$new_marker_id = $this->map_model->insert_marker( $data );
		
		if( $new_marker_id ) {
			$data['id'] = $new_marker_id;
			$data['description'] = "";

			$head_data = array("min_template"=>"image_upload", "title"=>$this->dist['site_title'].": Marcador");
			$this->load->view('head', $head_data);
				
			$this->load->view('marker_form', array('data' => $data) );
			$this->load->view('foot');
		} else {
			show_error('dist_general_error');
		}
	}	
	
	public function list_images($marker_id) {
		$this->load->helper('image_helper');
		$files = $this->image_model->get_images($marker_id);
		$this->load->view('marker_images', array('files' => $files));
	}
	
	public function get_image($image_id) {
		$image_data = $this->image_model->get_by_id($image_id);
		//print_r($image_data);
		$this->load->view("marker_image_single", array("image"=>$image_data));
	}
}
