<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id = intval($_POST['id']);
$lines = isset($_POST['lines'])?intval($_POST['lines']):0;
$markers = isset($_POST['markers'])?intval($_POST['markers']):0;
$access = isset($_POST['access'])?intval($_POST['access']):0;
if(!($id>0)){exit(json_encode(array("error"=>"Не передан идентификатор")));}

$id_user = $_COOKIE["user"];

$q = $mysqli->query("SELECT id FROM routes WHERE id_author={$id_user} AND id={$id} LIMIT 1");
if(!$q || $q->num_rows!=1){
	exit(json_encode(array("error"=>"Очистить маршрут может только его создатель. \r\n".$mysqli->error)));
}



if($markers>0){ $mysqli->query("DELETE FROM route_objects WHERE id_route={$id} AND id_typeobject=1"); }
if($lines>0){ $mysqli->query("DELETE FROM route_objects WHERE id_route={$id} AND id_typeobject=2"); }
if($access>0){ $mysqli->query("DELETE FROM route_editors WHERE id_route={$id}"); }

exit(json_encode(array("success"=>true )));							


?>