<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных

if(!isset($_POST['id1'])){die(json_encode(array("error"=>"Не передан идентификатор основного маршрута")));}
if(!isset($_POST['id2'])){die(json_encode(array("error"=>"Не передан идентификатор второго маршрута")));}
$id1 = intval($_POST['id1']);
$id2 = intval($_POST['id2']);
$id_user = isset($_COOKIE["user"])?$_COOKIE["user"]:0;

$q = $mysqli->query("SELECT coordinates, distance FROM route_objects WHERE id={$id1} LIMIT 1");
if(!$q){die(json_encode(array("error"=>"Ошибка получения данных 1 ".$mysqli->error)));}
$r = $q->fetch_assoc();
$arr = json_decode($r['coordinates']);
$dist1 = $r['distance'];
$q = $mysqli->query("SELECT coordinates, distance FROM route_objects WHERE id={$id2} LIMIT 1");
if(!$q){die(json_encode(array("error"=>"Ошибка получения данных 1 ".$mysqli->error)));}
$r = $q->fetch_assoc();
$arr2 = json_decode($r['coordinates']);
$dist2 = $r['distance'];
$q = $mysqli->query("UPDATE route_objects 
	SET `coordinates`='".json_encode(array_merge($arr, $arr2))."', distance=".($dist2+$dist1).", id_editor={$id_user}, date_last_modif='".date('d.m.Y H:i:s')."' 
	WHERE id={$id1}
");

	if($q){
		$mysqli->query("DELETE FROM `route_objects` WHERE id={$id2}");
		exit(json_encode(array("success"=>$dist2+$dist1)));
	} else {exit(json_encode(array("error"=>"Ошибка обновления данных".$mysqli->error)));}

?>