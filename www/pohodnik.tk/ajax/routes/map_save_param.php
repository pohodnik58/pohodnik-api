<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id = intval($_POST['id']);
$name = $mysqli->real_escape_string($_POST['name']);
$value = $mysqli->real_escape_string($_POST['value']);
$id_user = $_COOKIE["user"];
$q = $mysqli->query("UPDATE `routes` SET `{$name}`='{$value}' WHERE id={$id}");
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
die(json_encode(array("success"=>true)));