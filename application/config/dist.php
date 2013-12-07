<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DISTRIBUTION SETTINGS
| -------------------------------------------------------------------
| The settings below are specific to the Distribution of Xumb
*/

$config['dist'] = array(
	'site_title' => 'Xumb',
	'image_settings' => array(
		'thumb_sizes' => array(80, 150), // size of thumbs to generate
		'allowed_types' => array('jpeg', 'jpg', 'png'),
		'min_image_size' => '200'
	),
	'upload' => array(
		'path' => './files/',
		'max_size' => (8*1024) // this should be <= your php.ini config
	),
	'use_categories'=> TRUE,
	'default_category' => 99,
	'max_images_marker'=> 0 // use 0 for unlimited images
);