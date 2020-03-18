<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_hiking = intval($_GET['id_hiking']);
$id_user = $_COOKIE["user"];

$q = $mysqli->query("SELECT route_objects.id, route_objects.name, route_objects.`desc` , route_objects.coordinates
					FROM  hiking  
					LEFT JOIN route_objects ON route_objects.id_route = hiking.id_route
					WHERE  hiking.id={$id_hiking} AND route_objects.id_typeobject=1");
if(!$q){die(json_encode(array("error"=>"Error. ".$mysqli->error)));}
while($r=$q->fetch_assoc()){
$r['coordinates'] = json_decode($r['coordinates']);
$result[] = $r;
}

die(json_encode($result));
