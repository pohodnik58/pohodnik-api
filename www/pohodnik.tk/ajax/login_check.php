<?php
    Header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); //Дата в прошлом
    Header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    Header("Pragma: no-cache"); // HTTP/1.1
    Header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	function ErrorCatcher($errno, $errstr, $errfile, $errline){
		die(json_encode(array("error"=>$errstr."\n(".$errfile.":".$errline.")", "errno"=>$errno, "file"=>$errfile, "line"=>$errline )));
		exit();
		return false;
	}
	
	set_error_handler("ErrorCatcher");

	include("../blocks/db.php"); //подключение к БД
	include("../blocks/global.php"); //подключение к БД


	$user = array();
	$id		=	isset($_COOKIE["user"])?$_COOKIE["user"]:false;
	$hash	=	isset($_COOKIE["hash"])?$_COOKIE["hash"]:false;
	$result = false;
	if($id && $hash){
		$q = $mysqli->query("	SELECT 
									users.id, users.name, users.surname, users.photo_50, user_hash.date_start AS session_start
								FROM users 
									LEFT JOIN user_hash ON user_hash.id_user = users.id 
								WHERE 
									user_hash.hash='{$hash}' AND users.id={$id} 
								LIMIT 1");
		if(!$q){die(err(array("error"=>$mysqli->error, "result"=>false)));}
		if($q && $q->num_rows===1){
			$user = $q->fetch_assoc();
			$result=true;
		} else {
		    die(err('Пользователь не найден'));
		}
	};
	die(out(array("result"=>$result, "user"=>$user)));