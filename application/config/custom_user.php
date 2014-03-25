<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| CUSTOM USER FIELDS
| -------------------------------------------------------------------
| Use the array below to customize you User Profile 
| with your own custom fields. 
| Remember to also update the views and the database
| table accordingly for it to work properly
|
| The array below is an example and a template for
| the format to be used.. If you don't want to use
| those just leave an empty array
|	$config['custuser_info'] = array();
*/
$config['custuser_info'] = array(
	array(
	'form_name' => 'city',  // the name for the form variable to be used
							// this should match your view
	'table_column' => 'city', // self-explanatory
	'label' => 'City', // to be used in error messages
	'form_validation' => 'min_length[3]|max_length[100]'
	// refer to CodeIgniter documentation for validation rules
	),
	array(
	'form_name' => 'country',
	'table_column' => 'country',
	'label' => 'Country',
	'form_validation' => 'min_length[3]|max_length[45]'
	),
	array(
	'form_name' => 'zip_code',
	'table_column' => 'zip_code',
	'label' => 'ZIP',
	'form_validation' => 'numeric|min_length[5]|max_length[20]'
	)
);