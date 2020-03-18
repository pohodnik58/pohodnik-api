<?php
include("../blocks/db.php"); //подключение к БД
$login =		$mysqli->real_escape_string(trim(strtolower($_POST["login"])));
$pass  =		$mysqli->real_escape_string(trim($_POST["pass"]));
$hash  =		uniqid("poh").rand(100,999).'x';

$q = $mysqli->query("SELECT id_user, password FROM user_login_variants WHERE login='".$login."' LIMIT 1");
if($q && $q->num_rows===1){
	$res = $q->fetch_assoc();
	$id_user = $res["id_user"];
	if($res["password"]===md5(md5($pass))){
		if($mysqli->query("INSERT INTO user_hash SET hash='{$hash}', date_start=NOW(), id_user={$id_user}")){
			setcookie("hash", $hash,time()+86400*7,"/");
			setcookie("user", $res["id_user"],time()+86400*7,"/");
			echo(json_encode(array("user"=>$id_user)));
		}else{echo(json_encode(array("error"=>"Ошибка авторизации".$mysqli->error)));}
	} else {
		echo(json_encode(array("error"=>"Неверный пароль")));
	};
}else{
	echo(json_encode(array("error"=>"Ты кто такой? Давай до свидания!")));
};
?>