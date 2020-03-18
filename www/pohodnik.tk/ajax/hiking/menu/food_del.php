<?php
include("../../../blocks/db.php"); //подключение к БД
include("../../../blocks/for_auth.php"); //Только для авторизованных

$id = intval($_POST['id']);
$q =  $mysqli->query("SELECT id_hiking FROM hiking_menu WHERE id={$id} LIMIT 1");
if($q){
	$r = $q->fetch_assoc();
	$id_hiking = $r['id_hiking'];
	$id_user = $_COOKIE["user"];
	if(!($id_hiking>0)){die(json_encode(array("error"=>"id_hiking is undefined")));}
	$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_user} LIMIT 1");
	if($q && $q->num_rows===0){
		$q = $mysqli->query("SELECT id FROM hiking_editors WHERE id_hiking={$id_hiking}  AND is_cook=1  AND id_user = {$id_user} LIMIT 1");
		if($q && $q->num_rows===0){
			die(json_encode(array("error"=>"Нет доступа")));
		}
	}

}

$q = $mysqli->query("DELETE FROM hiking_menu WHERE id={$id}");
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
die(json_encode(array("success"=>true)));