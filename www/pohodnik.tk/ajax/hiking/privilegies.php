<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id = intval($_GET['id']);
$id_user = $_COOKIE["user"];
$res['is_admin'] = 0;
$res = array();
$q = $mysqli->query("SELECT is_guide, is_cook, is_writter, is_financier FROM hiking_editors WHERE id_hiking={$id} AND id_user={$id_user}") OR die(json_encode(array("error"=>$mysqli->error)));
if($q && $q->num_rows>0){ $res = $q->fetch_assoc();}
$q1 = $mysqli->query("SELECT id FROM hiking WHERE id={$id} AND id_author={$id_user} LIMIT 1");
if($q1 && $q1->num_rows===1){ $res['is_admin'] = 1; }


echo json_encode($res);