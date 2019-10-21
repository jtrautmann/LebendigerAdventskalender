#!/usr/machine/bin/php-cgi
<?php

$targetdir = '../img_tmp/';	// with trailing slash

// Include the upload handler class
require_once "handler.php";

$uploader = new UploadHandler();

// Specify the list of valid extensions, ex. array("jpeg", "xml", "bmp")
$uploader->allowedExtensions = array("jpeg","jpg","png","gif","tiff","tif");

// Specify max file size in bytes.
$uploader->sizeLimit = 5000000;

// Specify the input name set in the javascript.
$uploader->inputName = "qqfile"; // matches Fine Uploader's default inputName value by default

// If you want to use the chunking/resume feature, specify the folder to temporarily save parts.
$uploader->chunksFolder = "chunks";

$method = $_SERVER["REQUEST_METHOD"];
if ($method == "POST") {
	header("Content-Type: text/plain");

	// Call handleUpload() with the name of the folder, relative to PHP's getcwd()
	$result = $uploader->handleUpload($targetdir);

	// To return a name used for uploaded file you can use the following line.
	$result["uploadName"] = $uploader->getUploadName();
	
	if($result['success']){
		list($oldWidth, $oldHeight, $type) = getimagesize($targetdir.$result['uploadName']);
		if($oldWidth>1065 || $oldHeight>1065){
			switch($type) {
				case IMAGETYPE_GIF:
					$oldImage = imagecreatefromgif($targetdir.$result['uploadName']);
					break;
				case IMAGETYPE_PNG:
					$oldImage = imagecreatefrompng($targetdir.$result['uploadName']);
					break;
				default:
					$oldImage = imagecreatefromjpeg($targetdir.$result['uploadName']);
			}
			$exif = exif_read_data($targetdir.$result['uploadName']);
			if ($exif && isset($exif['Orientation'])) {
				$ort = $exif['Orientation'];
				if($ort == 6 || $ort == 5) $oldImage = imagerotate($oldImage, 270, null);
				elseif($ort == 3 || $ort == 4) $oldImage = imagerotate($oldImage, 180, null);
				elseif($ort == 8 || $ort == 7) $oldImage = imagerotate($oldImage, 90, null);
				if($ort == 5 || $ort == 4 || $ort == 7) imageflip($oldImage, IMG_FLIP_HORIZONTAL);
			}
			$oldWidth = imagesx($oldImage);
			$oldHeight = imagesy($oldImage);
			if ($oldHeight < $oldWidth) {
				$newWidth = 1065;
				$newHeight = 1065*$oldHeight/$oldWidth; 
			}
			else { 
				$newHeight = 1065;
				$newWidth = 1065*$oldWidth/$oldHeight; 
			}
			$newImage = imagecreatetruecolor($newWidth, $newHeight);
			imagecopyresampled($newImage, $oldImage, 0, 0, 0, 0, $newWidth, $newHeight, $oldWidth, $oldHeight);
			unlink($targetdir.$result['uploadName']);
			$expl = explode('.',$result['uploadName'],-1);
			$result['uploadName'] = $expl[0].'.jpg';
			imagejpeg($newImage, $targetdir.$result['uploadName']);
			imagedestroy($oldImage);
			imagedestroy($newImage);
		}
	}
	
	echo json_encode($result);
	
	// Only keep 10 newest files
	if ($handle = opendir($targetdir)) {
	    while (false !== ($file = readdir($handle))) {
		   if ($file != '.' && $file != '..') $files[filemtime($targetdir.$file)] = $file;
	    }
	    closedir($handle);
	}
	if(count($files)) {
		ksort($files);
		for($i=1; $i<=count($files)-10; $i++){
			unlink($targetdir.current($files));
			next($files);	
		}
	}
}
else {
	header("HTTP/1.0 405 Method Not Allowed");
}

?>