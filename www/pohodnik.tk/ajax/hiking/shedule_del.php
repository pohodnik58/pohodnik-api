<?php //recipes_product_add.php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_user = $_COOKIE["user"];
$id = intval($_POST['id']);
if(!($id>0)){die(json_encode(array("error"=>"Undefined ID")));}
$q = $mysqli->query("SELECT DAY(d1) AS d, id_food_act,id_hiking  FROM hiking_schedule WHERE id={$id} LIMIT 1");
if(!$q || $q->num_rows===0){die(json_encode(array("error"=>"Ошибка. ".$mysqli->error)));}
$r = $q->fetch_assoc();
if($r['id_food_act']>0){
	$mysqli->query("DELETE FROM hiking_menu WHERE date='".$r['d']."' AND id_act=".$r['id_food_act']." AND id_hiking=".$r['id_hiking']." ");
	
}	

if($mysqli->query("DELETE FROM `hiking_schedule` WHERE id={$id}")){
	die(json_encode(array("success"=>true)));
} else {
	die(json_encode(array("error"=>"Ошибка. ".$mysqli->error)));
}