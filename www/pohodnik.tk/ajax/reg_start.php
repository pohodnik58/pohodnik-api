<?php
include("../blocks/db.php"); //подключение к БД
$name=		$mysqli->real_escape_string(trim($_POST["name"]));
$surname=	$mysqli->real_escape_string(trim($_POST["surname"]));
$login=		$mysqli->real_escape_string(trim(strtolower($_POST["login"])));
$pass=		$mysqli->real_escape_string(trim($_POST["pass"]));
$hash  =	uniqid("poh").rand(100,999).'r';

$q = $mysqli->query("SELECT id_user FROM user_login_variants WHERE login='{$login}' LIMIT 1");
if($q && $q->num_rows===1){exit(json_encode(array("error"=>"Пользователь с таким логином уже существует.")));
}else if($mysqli->query("INSERT INTO users SET
					`name`='{$name}',
					`surname`='{$surname}',
					`reg_date`=NOW()")){
	$id_user = $mysqli->insert_id;
				
					
	$q = $mysqli->query("INSERT INTO user_login_variants SET login='{$login}', id_user={$id_user}, password='".md5(md5($pass))."'");
	if(!$q){die(json_encode(array("error"=>"Ошибка добавления варианта залогинивания".$mysqli->error)));}
	
	
	$q = $mysqli->query("INSERT INTO `user_hash`(`id_user`, `hash`, `date_start`) VALUES ({$id_user},'{$hash}',NOW())");
	if(!$q){die(json_encode(array("error"=>"Ошибка добавления токена ".$mysqli->error)));}	
	
	setcookie("hash", $hash,time()+86400*7,"/");
	setcookie("user", $id_user,time()+86400*7,"/");
	exit(json_encode(array("user"=>$id_user)));
}else{
	exit(json_encode(array("error"=>"Ошибка при добавлении пользователя. \r\n".$mysqli->error)));
};
?>