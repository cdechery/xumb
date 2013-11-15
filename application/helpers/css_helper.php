<?php
/*
 * Helper to load CSS libs
 */
 
function load_css($css_libs) {
	
    if( !is_array($css_libs) ){
        return false;
    }
     
    $return = '<link rel="stylesheet" type="text/css" href="'. base_url().'css/style.css"/>';
    foreach($css_libs as $lib){
        $return .= '<link rel="stylesheet" type="text/css" href="' . base_url() . 'css/' . $lib . '.css"/>' . "\n";
    }
    
    return $return;
}
?>
