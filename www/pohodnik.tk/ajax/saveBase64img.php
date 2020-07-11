<?php
	include("../blocks/for_auth.php"); //Только для авторизованных
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	function ErrorCatcher($errno, $errstr, $errfile, $errline){
		die(json_encode(array("error"=>$errstr."\n(".$errfile.":".$errline.")", "errno"=>$errno, "file"=>$errfile, "line"=>$errline )));
		exit();
		return false;
	}
	set_error_handler("ErrorCatcher");
	$imageData = $_POST['data'];
	$name = isset($_POST['name'])?$_POST['name']:'file_'.md5(time());
	$folder = isset($_POST['folder'])?$_POST['folder']:'images/custom';
	
	list($type, $imageData) = explode(';', $imageData);
	list(,$extension) = explode('/',$type);
	list(,$imageData)      = explode(',', $imageData);
	$fileName = $name.'.'.$extension;
	$imageData = base64_decode($imageData);
	if(file_exists ('../'.$folder.$fileName )){ $fileName = '_'.$name.'.'.$extension; }
	file_put_contents('../'.$folder.$fileName, $imageData);
	if(file_exists ('../'.$folder.$fileName )){ 
	$resSizes= array();

		if(isset($_POST['sizes'])){
			if(is_array($_POST['sizes'])){$sizes = $_POST['sizes'];} else { $sizes = explode(",", $_POST['sizes']);}
			if(count($sizes)>0){
				list($width, $height) = getimagesize('../'.$folder.$fileName);
				$coeff = $width / $height;
				require_once('lib/php-image-magician/php_image_magician.php');
				foreach ( $sizes as $size) {
					if(strpos($size,"/")>0){
						$tmp = explode("/",$size);
						$width = intval($tmp[0]);
						$height = intval($tmp[1]);

					} else {
						$width = intval($size);
						$height = intval($size) / $coeff;
					}
					$magicianObj = new imageLib('../'.$folder.$fileName);
					$magicianObj -> resizeImage($width, $height, 'crop');
					$resFile = $folder.$name.'_'.$width."_".$height.'.'.$extension;
					$magicianObj -> saveImage('../'.$resFile);
					$resSizes[$size] = $resFile;

				}
			}
		}

		die(json_encode(array('success'=>true,'filename'=>$fileName, 'name'=>$fileName, 'extention'=>$extension, 'type'=>$type, 'folder'=>$folder, 'sizes'=>$resSizes )));
	} else {
		die(json_encode(array("error"=>"Не удалось сохранить файл")));	
	}