<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DISTRIBUTION SETTINGS
| -------------------------------------------------------------------
| The settings below are specific to the Distribution of Xumb
*/

$config['dist'] = array(
	'site_title' => 'Xumb',
	'email' => array('from'=>'webmaster@somesite.org', 'name'=>'Your Name'),
	'update_tool' => array(
		'password' => '###changethis###', // if you don't change this, the Update Tool will not work
		'skip_files' => ''
	),
	'googlemaps' => array(
		'center' => 'Rio de Janeiro', // where to center your map
		'zoom' => '13', // default zoom level
		'geocodeCaching' => TRUE, // cache your locations in the database
		'minifyJS' => TRUE, // minify the generated JS code
		'places' => FALSE,
		'cluster' => TRUE, // whether to cluster markers together
		'clusterGridSize' => 40,
		'sensor' => TRUE //whether to allow for detection of user's location 
	),
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
