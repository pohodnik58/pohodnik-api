<?php
ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$id_user = $_COOKIE["user"];
$id_hiking = intval($_POST['id_hiking']);
$ava_folder = 'images/hiking/bg/';

$sizes = array(50,100,200);
/*
print_r($_FILES['file']);
exit();
*/
	if(!file_exists('../../'.$ava_folder)){
		mkdir('../../'.$ava_folder, 0777, true);
		chmod('../../'.$ava_folder, 0777);
	}
	
	

	
	$a = explode (".", basename($_FILES['file']['name']));
	$uploadedFile =	str_replace('.'.$a[count($a)-1], "", basename($_FILES['file']['name']));
	$uploadedFile =  'bg.'.$a[count($a)-1];
	if(is_uploaded_file($_FILES['file']['tmp_name'])){

		$path = '../../'.$ava_folder."orig_".$uploadedFile;
		if(move_uploaded_file($_FILES['file']['tmp_name'],$path)){
			list($width, $height) = getimagesize($path);
		
		
		/*if(!file_exists('../../'.$ava_folder."h_".$id_hiking."_".$uploadedFile)){
			unlink('../../'.$ava_folder."h_".$id_hiking."_".$uploadedFile);
		}*/
		
		
		
			$targ_w = 1590;
			$targ_h = 400;
			$jpeg_quality = 90;
			$img_r = imagecreatefromjpeg($path);
			$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
			imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
			$targ_w,$targ_h,$_POST['w'],$_POST['h']);
			imagejpeg($dst_r, '../../'.$ava_folder."h_".$id_hiking."_".$uploadedFile,$jpeg_quality);
			
			unlink($path);
			$q=$mysqli->query("UPDATE `hiking` SET `bg`='".$ava_folder."h_".$id_hiking."_".$uploadedFile."'  WHERE id={$id_hiking}");
			if(!$q){exit(json_encode(array("error"=>"Ошибка\r\n".$mysqli->error)));}
			if($q){
				exit(json_encode(array("success"=>true,
					"ava"=>$ava_folder."h_".$id_hiking."_".$uploadedFile
				)));
			} else {
			
			}
			
		} else { 
			exit(json_encode(array("error"=> "Не могу переместить файл из временной папки")));
		}
	} else {
		exit(json_encode(array("error"=>"Файл не загружен ".$_FILES['file']['error'])));
	}
 

if(!$q){exit(json_encode(array("error"=>"Ошибка при редактировании пользовательских данных. \r\n")));}
echo(json_encode(array("msg"=>"Данные успешно обновлены. \r\n")));
?>
