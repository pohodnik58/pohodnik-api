<?php
	include("../blocks/db.php"); //подключение к БД
	$s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
	$user = json_decode($s, true);
	$user['network'] // - соц. сеть, через которую авторизовался пользователь
	$user['identity'] // - уникальная строка определяющая конкретного пользователя соц. сети
	$user['first_name'] // - имя пользователя
	$user['last_name'] // - фамилия пользователя
					
	$q = $mysqli->query("SELECT id_user FROM user_login_variants WHERE login='".$user['identity']."' LIMIT 1");
if($q && $q->num_rows===1){
	$res = $q->fetch_assoc();
	$id_user = $res["id"];

	if($mysqli->query("UPDATE users SET hash='".$_POST['token']."' WHERE id=".$res["id"])){
		setcookie("hash", $_POST['token'],time()+86400*7,"/");
		setcookie("user", $res["id"],time()+86400*7,"/");
		echo(json_encode(array("user"=>$res["id"])));
	}else{echo(json_encode(array("error"=>"Ошибка авторизации".$mysqli->error)));}

}else{
	if( $mysqli->query("INSERT INTO `users` SET 
						`email`='',
						`hash`='".$_POST['token']."',
						`name`='".$user['first_name']."',
						`surname`='".$user['last_name']."'
				   ")){
	
		$id_user = $mysqli->insert_id;
		
			$q = $mysqli->query("INSERT INTO user_login_variants SET login='".$user['identity']."', id_user={$id_user}, network='".$user['network']."'")
			if(!$q){die(json_encode(array("error"=>"Ошибка добавления варианта залогинивания".$mysqli->error)));}
			setcookie("hash", $_POST['token'],time()+86400*7,"/");
			setcookie("user", $id_user,time()+86400*7,"/");
			echo(json_encode(array("user"=>$id_user, "new"=>true)));	
	}
	
};