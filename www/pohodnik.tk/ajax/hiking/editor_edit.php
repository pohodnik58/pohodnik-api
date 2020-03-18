<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id = intval($_POST['id']);
$name = $mysqli->real_escape_string($_POST['name']);
$value = $mysqli->real_escape_string($_POST['value']);
$id_user = $_COOKIE["user"];

$q = $mysqli->query("SELECT id FROM hiking WHERE id_author={$id_user} LIMIT 1");
if(!$q || $q->num_rows===0){ die( json_encode(array("error"=>"Access Denied! ".$mysqli->error))); }
if(!in_array($name,array('is_guide','is_cook', 'is_writter','is_financier'))){
	die( json_encode(array("error"=>"Incorrect name value"))); 
}
$q = $mysqli->query("UPDATE hiking_editors SET `{$name}`={$value} WHERE id={$id}");
if($q){
	die(json_encode(array("success"=>true)));
} else {
	die(json_encode(array("error"=>$mysqli->error)));
}