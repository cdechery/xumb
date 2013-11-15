<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
	Image Helper - Custom Helper (not a part of CodeIgniter's
	
	All functions regarding Imagem manipulation goes here
*/

function add_thumb_suffix(&$data, $key, $thumb_size) {
	$data->filename = thumb_filename( $data->filename, $thumb_size );
}

function thumb_filename($filename, $thumbSize) {
	$newFileArray = pathinfo( $filename );
	return $newFileArray['filename']."_t".$thumbSize.".".$newFileArray['extension'];
	
}

function create_square_cropped_thumb($imgFullPath, $size) {

	// generate thumb
	$img = imagecreatefromjpeg( $imgFullPath );
	$width = imagesx( $img );
	$height = imagesy( $img );

	$newH = $newW = 0;
	$limiting_dim = 0;

	/* Calculate the New Image Dimensions */
	if( $height > $width){
		/* Portrait */
		$newW = $size;
		$newH = $height * ( $size / $newW );
		$limiting_dim = $width;
	}else{
		/* Landscape */
		$newH = $size;
		$newW = $width * ( $size / $newH );
		$limiting_dim = $height;
	}

	$tmp_img = imagecreatetruecolor( $size, $size );
	imagecopyresampled( $tmp_img, $img , 0 , 0 , ($width-$limiting_dim )/2 , ( $height-$limiting_dim )/2 , $size , $size , $limiting_dim , $limiting_dim );

	// novo nome
	$newFileArray = pathinfo( $imgFullPath );
	$thmbFileName = $newFileArray['dirname']."/".$newFileArray['filename']."_t$size.".$newFileArray['extension'];

	imagejpeg( $tmp_img, $thmbFileName );

	imagedestroy( $img );
	imagedestroy( $tmp_img );
}

