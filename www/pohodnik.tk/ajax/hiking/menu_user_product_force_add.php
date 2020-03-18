<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
include("../../blocks/err.php"); //Только для авторизованных
$result = array();

$id_author = $_COOKIE["user"];
$id_hiking = isset($_POST['id_hiking'])?intval($_POST['id_hiking']):0;
$id_product = isset($_POST['id_product'])?intval($_POST['id_product']):0;
$id_user = isset($_POST['id_user'])?intval($_POST['id_user']):0;

if(!($id_hiking>0)){die(json_encode(array("error"=>"id_hiking is undefined")));}
if(!($id_product>0)){die(json_encode(array("error"=>"id_product is undefined")));}
if(!($id_user>0)){die(json_encode(array("error"=>"id_user is undefined")));}



$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_author} LIMIT 1");
if($q && $q->num_rows===0){
	$q = $mysqli->query("SELECT id FROM hiking_editors WHERE id_hiking={$id_hiking}  AND is_cook=1  AND id_user = {$id_author} LIMIT 1");
	if($q && $q->num_rows===0){
		die(json_encode(array("error"=>"Нет доступа")));
	}
}
$mysqli->query("DELETE FROM `hiking_menu_products_force` WHERE `id_hiking`={$id_hiking} AND `id_product`={$id_product}");
if($mysqli->query("INSERT INTO `hiking_menu_products_force` SET `id_hiking`={$id_hiking},`id_product`={$id_product},`id_user`={$id_user}, date=NOW(), id_author={$id_author}")){
	die(json_encode(array('success'=>true, 'id'=>$mysqli->insert_id)));
} else {
	die(json_encode(array('error'=>$mysqli->error)));
}