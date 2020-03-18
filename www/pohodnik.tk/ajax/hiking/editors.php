<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id = intval($_GET['id']);
$id_user = $_COOKIE["user"];

$q = $mysqli->query("SELECT  users.name, users.surname, 1 as author FROM hiking LEFT JOIN users ON hiking.id_author = users.id 
WHERE hiking.id={$id} LIMIT 1");
$res = array();
$r = $q->fetch_assoc();
$res[] = $r;


$q = $mysqli->query("SELECT hiking_editors.id, users.name, users.surname, hiking_editors.is_guide, hiking_editors.is_cook, hiking_editors.is_writter, hiking_editors.is_financier FROM hiking_editors LEFT JOIN users ON hiking_editors.id_user = users.id 
WHERE hiking_editors.id_hiking={$id}") OR die(json_encode(array("error"=>$mysqli->error)));

while($r = $q->fetch_assoc()){
	$res[] = $r;
}

echo json_encode($res);



