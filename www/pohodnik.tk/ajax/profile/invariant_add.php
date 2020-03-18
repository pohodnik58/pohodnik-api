<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result=array();
$id_user = $_COOKIE["user"];

$login =		isset($_POST["login"])?$mysqli->real_escape_string($_POST["login"]):'';
$hash =		isset($_POST["hash"])?$mysqli->real_escape_string($_POST["hash"]):'';
$network =		isset($_POST["network"])?$mysqli->real_escape_string($_POST["network"]):'login';
if(!strlen($login)>0){die(json_encode(array("error"=>"Login is required")));}
$password = isset($_POST["password"])?md5( md5( $_POST["password"] )):'';
if(!strlen($hash)>0 && !strlen($password)>0){die(json_encode(array("error"=>"Password or Hash  is required")));}


$q = $mysqli->query("SELECT `id`, `id_user`, `login`, `password`, `network` FROM `user_login_variants` WHERE login='{$login}' LIMIT 1");
if(!$q){die(json_encode(array('error'=>"Не удается получить данные. ".$mysqli->error)));}
if($q && $q->num_rows===1){
	$r = $q->fetch_assoc();
	die(json_encode(array('error'=>"Эта учетная запись уже добавлена".($r['id_user'] == $id_user?' вами':' кем-то другим...'))));
}


$q = $mysqli->query("INSERT INTO `user_login_variants`(`id_user`, `login`, `password`, `network`) VALUES ({$id_user},'{$login}','{$password}','{$network}')");
if($q){
	
	if(isset($_POST['data'])){
		$data = $_POST['data'];
		$updateFielsd = array();
		
		if(isset($data['first_name'])){ $updateFielsd[] = "`name`='".$data['first_name']."'"; }
		if(isset($data['last_name'])){ $updateFielsd[] = "`surname`='".$data['last_name']."'"; }
		if(isset($data['sex'])){ $updateFielsd[] = "`sex`='".($data['sex']==1?2:1)."'"; }
		if(isset($data['bdate'])){ $updateFielsd[] = "`dob`='".date('Y-m-d H:i:s', strtotime($data['bdate']))."'"; }
		if(isset($data['photo_50'])){ $updateFielsd[] = "`ava`='".$data['photo_50']."'"; }
		if(isset($data['photo_100'])){ $updateFielsd[] = "`photo_100`='".$data['photo_100']."'"; }
		if(isset($data['photo_200_orig'])){ $updateFielsd[] = "`photo_200_orig`='".$data['photo_200_orig']."'"; }
		if(isset($data['photo_max'])){ $updateFielsd[] = "`photo_max`='".$data['photo_max']."'"; }
		if(isset($data['photo_max_orig'])){ $updateFielsd[] = "`photo_max_orig`='".$data['photo_max_orig']."'"; }

		if(count($updateFielsd)>0){
			$q = $mysqli->query("UPDATE users SET ".implode(',',$updateFielsd)." WHERE id=".$id_user);
			if(!$q){die(json_encode(array('error'=>"Не удается обновить. ".$mysqli->error)));}
		}
		
	}
	
	die(json_encode(array('success'=>true)));
	
} else {
	die(json_encode(array('error'=>"Не удается получить данные. ".$mysqli->error)));
}