<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/** 
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 *
 * See http://code.google.com/p/minify/wiki/CustomSource for other ideas
 **/

return array(
	'image_upload_js' => array('//js/jquery-1.10.2.min.js', '//js/bootstrap.min.js', '//js/ajaxfileupload.js', '//js/messi.min.js', '//js/site.js', '//js/jquery.fancybox.pack.js'),
	'image_upload_css' => array('//css/bootstrap.min.css', '//css/style.css', '//css/messi.min.css', '//css/jquery.fancybox.css'),
	'image_view_js' => array('//js/jquery-1.10.2.min.js', '//js/bootstrap.min.js','//js/messi.min.js', '//js/site.js', '//js/jquery.fancybox.pack.js'),
	'image_view_css' => array('//css/bootstrap.min.css', '//css/style.css','//css/messi.min.css', '//css/jquery.fancybox.css'),
	'basic_js' => array('//js/jquery-1.10.2.min.js', '//js/bootstrap.min.js','//js/messi.min.js', '//js/site.js'),
	'basic_css' => array('//css/style.css', '//css/bootstrap.min.css', '//css/messi.min.css')
);