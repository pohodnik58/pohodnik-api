<?php

include("../../../blocks/db.php"); //подключение к БД
include("../../../blocks/for_auth.php"); //Только для авторизованных
include("../../../blocks/err.php"); //Только для авторизованных
$id_user = isset($_POST['id_user'])?intval($_POST['id_user']):$_COOKIE["user"];
$id_author = $_COOKIE["user"];
$id_hiking = $_POST['id_hiking'];
if(!($id_hiking>0)){die(json_encode(array("error"=>"id_hiking is undefined")));}
$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_author} LIMIT 1");
if($q && $q->num_rows===0){
	$q = $mysqli->query("SELECT id FROM hiking_editors WHERE id_hiking={$id_hiking}  AND is_financier=1  AND id_user = {$id_author} LIMIT 1");
	if($q && $q->num_rows===0){
		die(json_encode(array("error"=>"Нет доступа")));
	}
}

$ava_folder = 'receipts/'.$id_user;

$name = isset($_POST['name']) && strlen($_POST['name'])>0?$mysqli->real_escape_string(trim($_POST['name'])):"Чек от ".date('d.m.Y');

$summ = floatval($_POST['summ']);
$img = $_POST['img'];
$img = str_replace('data:image/png;base64,', '', $img);
$img = str_replace('data:image/jpeg;base64,', '', $img);
$img = str_replace('data:image/jpg;base64,', '', $img);

$img = str_replace(' ', '+', $img);
$data = base64_decode($img);
$uploadedFile = md5(time().'sd234dfs423').'.jpg';



	if(!file_exists('../../../'.$ava_folder)){
		mkdir('../../../'.$ava_folder, 0777, true);
		chmod('../../../'.$ava_folder, 0777);
	}
	
	
	$ava_folder = $ava_folder.'/';
	$path = '../../../'.$ava_folder."orig_".$uploadedFile;
	if(file_put_contents($path, $data)){
		
		
		
			list($width, $height) = getimagesize($path);
		
			$coef = $width/$height;
			
			
			$targ_w = 600;
			$targ_h = 600/$coef;
			$jpeg_quality = 90;
			$img_r = imagecreatefromjpeg($path);
			$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
			imagecopyresampled($dst_r,$img_r,0,0,0,0,
			$targ_w,$targ_h,$width,$height);
			imagejpeg($dst_r, '../../../'.$ava_folder."receipt_600_".$uploadedFile,$jpeg_quality);
			

			$targ_w = 100;
			$targ_h = 100/$coef;
			$jpeg_quality = 70;
			$img_r = imagecreatefromjpeg($path);
			$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
			imagecopyresampled($dst_r,$img_r,0,0,0,0,
			$targ_w,$targ_h,$width,$height);
			imagejpeg($dst_r, '../../../'.$ava_folder."receipt_100_".$uploadedFile,$jpeg_quality);			
			
			
			$q=$mysqli->query("INSERT INTO `hiking_finance_receipt` SET `name`='{$name}',`date`=NOW(),`id_user`={$id_user},`id_author`={$id_author},`id_hiking`={$id_hiking},`img_600`='".$ava_folder."receipt_600_".$uploadedFile."',`img_100`='".$ava_folder."receipt_100_".$uploadedFile."', summ={$summ}");
			if(!$q){exit(json_encode(array("error"=>"Ошибка\r\n".$mysqli->error)));}
			if($q){
				
				unlink($path);
			
				exit(json_encode(array(
					"success"=>true,
					"img_100"=>$ava_folder."receipt_100_".$uploadedFile,
					"img_600"=>$ava_folder."receipt_600_".$uploadedFile
				)));
			} else {
			
			}
			
		
	} else {
		exit(json_encode(array("error"=>"Файл не загружен ")));
	}
 

if(!$q){exit(json_encode(array("error"=>"Ошибка при . \r\n")));}
echo(json_encode(array("success"=>true, "msg"=>"Данные успешно обновлены. \r\n")));
?>