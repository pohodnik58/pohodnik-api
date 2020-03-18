<?php
include("../../../blocks/db.php"); //подключение к БД
include("../../../blocks/for_auth.php"); //Только для авторизованных

$id = intval($_POST['id']);

if(isset($_POST['id'])){
	if(is_array($_POST['id'])){
		$wh = " id IN(".implode($_POST['id']).")";
	} else {
		$wh = " id = ".intval($_POST['id'])."";
	}
} else {
	
$wh = " 1 ";
if(isset($_POST['id_hiking'])){$wh .= " AND hiking_menu.id_hiking=".intval($_POST['id_hiking'])." ";}
if(isset($_POST['id_act'])){$wh .= " AND hiking_menu.id_act=".intval($_POST['id_act'])." ";}
if(isset($_POST['date'])){$wh .= " AND hiking_menu.date='".$mysqli->real_escape_string($_POST['date'])."' ";}
	
}
$updatArr = array();

if(isset($_POST['is_optimize'])){
	$updatArr[] = "is_optimize=".intval($_POST['is_optimize']);
}

if(isset($_POST['сorrection_coeff_pct'])){
	$updatArr[] = "сorrection_coeff_pct=".intval($_POST['сorrection_coeff_pct']);
}

if(isset($_POST['assignee'])){
	$updatArr[] = "assignee_user=".intval($_POST['assignee']);
}

$q = $mysqli->query("UPDATE hiking_menu SET ".implode(', ', $updatArr)." WHERE {$wh}");
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
die(json_encode(array("success"=>true)));