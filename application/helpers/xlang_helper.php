<?php
/*
 * Custom Xumb helper to provide simple methods for loading Language Lines and Labels, with parameters
 */
 /**
     * Fetch a single line of text from the language array. Takes variable number
     * of arguments and supports wildcards in the form of '%1', '%2', etc.
     * Overloaded function.
     *
     * @access public
     * @return mixed false if not found or the language string
     */
    function xlang() {
        //get the arguments passed to the function
        $args = func_get_args();

        //count the number of arguments
        $c = count($args);

        //if one or more arguments, perform the necessary processing
        if ($c) {
            $CI =& get_instance();

            //first argument should be the actual language line key
            //so remove it from the array (pop from front)
            $line = array_shift($args);
            
            //check to make sure the key is valid and load the line
            $line = ($line == '' OR !isset($CI->lang->language[$line])) ? FALSE : $CI->lang->language[$line];
            
            //if the line exists and more function arguments remain
            //perform wildcard replacements
            if ($line && $args) {
                $i = 1;
                foreach ($args as $arg) {
                    $line = preg_replace('/\%'.$i.'/', $arg, $line);
                    $i++;
                }
            }
        } else {
            //if no arguments given, no language line available
            $line = false;
        }

        return $line;
    }

    function xlabel( $line ) {
        $CI =& get_instance();
        
        if( isset($CI->lang->language['dist_lbl_'.$line]) ) {
            return $CI->lang->language['dist_lbl_'.$line];
        } else {
            return "";
        }
    }
?>