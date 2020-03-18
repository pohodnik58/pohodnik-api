<?php

include("../../../blocks/db.php"); //подключение к БД
include("../../../blocks/for_auth.php"); //Только для авторизованных
$id_user = isset($_POST['id_user'])?intval($_POST['id_user']):$_COOKIE["user"];
$id_author = $_COOKIE["user"];
$id = intval($_POST['id']);


$q = $mysqli->query("SELECT url, id_hiking FROM hiking_tracks WHERE id={$id} LIMIT 1");
if($q && $q->num_rows===1){
	$r = $q->fetch_assoc();
	$id_hiking = $r['id_hiking'];
	$url = $r['url'];


	if(!($id_hiking>0)){die(json_encode(array("error"=>"id_hiking is undefined")));}
	$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_author} LIMIT 1");
	if($q && $q->num_rows===0){
		$q = $mysqli->query("SELECT id FROM hiking_editors WHERE id_hiking={$id_hiking}  AND is_guide=1  AND id_user = {$id_author} LIMIT 1");
		if($q && $q->num_rows===0){
			die(json_encode(array("error"=>"Нет доступа")));
		}
	}


	$q = $mysqli->query("DELETE FROM hiking_tracks WHERE id={$id}");
	if(!$q){exit(json_encode(array("error"=>"Ошибка при . \r\n")));}
	if(is_file('../../../'.$url)){
		unlink('../../../'.$url);
	}
	if(is_file('../../'.$url)){
		unlink('../../'.$url);
	}
	if(is_file('/'.$url)){
		unlink('/'.$url);
	}
	if(is_file($url)){
		unlink('/'.$url);
	}
	echo(json_encode(array("success"=>true, "msg"=>"Трек успешно удален", 'url'=>$url)));	
} else {
	exit(json_encode(array("error"=>"Запись не найдена")));	
}


?>