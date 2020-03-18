<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
$result = array();
$id = intval($_POST['id']);

$id_user = $_COOKIE["user"];


$q = $mysqli->query("SELECT users.name, users.surname, hiking.id_author, hiking.confirm_list_products, UNIX_TIMESTAMP(hiking.confirm_list_date) AS dt, hiking.confirm_list_user FROM hiking LEFT JOIN users on users.id = hiking.confirm_list_user WHERE LENGTH(hiking.confirm_list_products)>5 AND hiking.id={$id} LIMIT 1");
if($q && $q->num_rows===1){
	$r = $q->fetch_assoc();
	$res = json_decode($r['confirm_list_products']);
	$res['confirm'] = array(
		"user"=> $r['name']." ".$r['surname'],
		"date"=> date('d.m.Y',$r['dt']),
		"time"=> date('H:i', $r['dt'])
	);
	exit(json_encode($res));
}
?>