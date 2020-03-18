<?php
include("../../../blocks/db.php"); //подключение к БД
include("../../../blocks/for_auth.php"); //Только для авторизованных
include("../../../blocks/err.php"); //Только для авторизованных
$id_user = $_COOKIE["user"];
$id_hiking = intval($_POST['id_hiking']);
$id_route = intval($_POST['id_route']);
if(!($id_hiking>0)){die(json_encode(array("error"=>"id_hiking is undefined")));}
$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_user} LIMIT 1");
if($q && $q->num_rows===0){
	$q = $mysqli->query("SELECT id FROM hiking_members WHERE id_hiking={$id_hiking} AND id_user = {$id_user} LIMIT 1");
	if($q && $q->num_rows===0){
		die(json_encode(array("error"=>"Нет доступа")));
	}
}

$q = $mysqli->query("SELECT `id` FROM `hiking_route_variants` WHERE id_hiking={$id_hiking} AND id_route={$id_route} LIMIT 1");
if(!$q){die(json_encode(array("error"=>"Ji ".$mysqli->error)));}
if($q->num_rows===1){ die(json_encode(array("error"=>"Уже есть этот вариант маршрута"))); }

$q = $mysqli->query("INSERT INTO `hiking_route_variants`
					(`id_hiking`, `id_route`, `id_author`, `date`) VALUES 
					({$id_hiking},{$id_route},{$id_user},NOW())");
if(!$q){die(json_encode(array("error"=>"Ji ".$mysqli->error)));}
die(json_encode(array("success"=>true, "id"=>$mysqli->insert_id)));