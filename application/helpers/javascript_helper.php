<?php
/*
 * Helper to load Javascript libs
 */
 
function load_js($js_libs){
	
    if(!is_array($js_libs)){
        return false;
    }
     
    // the variable site_root is used in site.js where all major Ajax/Javascript calls are located
    $return = '<script type="text/javascript">var site_root=\''.base_url().'\';</script>'."\n";
    foreach($js_libs as $lib){
        $return .= '<script type="text/javascript" src="' . base_url() . 'js/' . $lib . '.js"></script>' . "\n";
    }
    
    return $return;
}
