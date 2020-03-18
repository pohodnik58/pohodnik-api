<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$name = $mysqli->real_escape_string(trim($_POST['name']));
$value = $mysqli->real_escape_string(trim($_POST['value']));
$id_user = $_COOKIE["user"];
$id = isset($_POST['id'])?intval($_POST['id']):0;
if(!($id>0)){die(json_encode(array("error"=>"ID is undefined")));}
$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id}  AND id_author = {$id_user} LIMIT 1");
if($q && $q->num_rows===0){
	$q = $mysqli->query("SELECT id FROM hiking_editors WHERE id_hiking={$id} AND id_user = {$id_user} LIMIT 1");
	if($q && $q->num_rows===0){
		die(json_encode(array("error"=>"Нет доступа")));
	}
}




$avai = explode(',','id_type,id_route,name,desc,text,start,finish,color,bg,id_region,ava,is_vacant_route');
if(!in_array($name, $avai)){die(json_encode(array("error"=>"Недопустимый параметр {$name}")));}

if($name=='bg'){
	$q = $mysqli->query("SELECT bg FROM hiking WHERE id={$id}");
	$r = $q->fetch_row();
	if(strlen($r[0])>0){
		unlink("../../".$r[0]);
	}
}

if($mysqli->query("UPDATE `hiking` SET  `{$name}`='{$value}'  WHERE id={$id} ")){
	exit(json_encode(array("success"=>"Данные успешно сохранены")));
}else{exit(json_encode(array("error"=>"Ошибка обновления . \r\n".$mysqli->error)));}