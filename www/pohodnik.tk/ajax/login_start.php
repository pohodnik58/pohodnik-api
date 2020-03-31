<?php
include("../blocks/global.php");
include("../blocks/err.php");
include("../blocks/db.php"); //подключение к БД

$login =		$mysqli->real_escape_string(trim(strtolower($_POST["login"])));
$pass  =		$mysqli->real_escape_string(trim($_POST["pass"]));
$is_remember =  isset($_POST['is_remember']) ? (boolean) $_POST['is_remember'] : false;
$hash  =		uniqid("poh").rand(100,999).'x';

$q = $mysqli->query("SELECT id_user, password FROM user_login_variants WHERE login='{$login}' LIMIT 1");
if($q && $q->num_rows===1){
	$res = $q->fetch_assoc();
	$id_user = $res["id_user"];
	if($res["password"]===md5(md5($pass))){
		if($mysqli->query("INSERT INTO user_hash SET hash='{$hash}', date_start=NOW(), id_user={$id_user}")){

		    $timeout = $is_remember ? time() + (86400 * 7) : time() + 3600;
			setcookie("hash", $hash, $timeout, "/");
			setcookie("user", $res["id_user"], $timeout, "/");

			echo(out(array("userId"=>$id_user)));
		} else {
		    echo(err(array(
                "message"=>"Ошибка авторизации",
                "error" => $mysqli->error
		    )));
		}
	} else {
		echo(err(array("Неверный пароль")));
	};
}else{
	echo(json_encode(array("error"=>"Ты кто такой? Давай до свидания!")));
};
?>