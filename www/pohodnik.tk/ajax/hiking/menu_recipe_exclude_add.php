<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
include("../../blocks/err.php"); //Только для авторизованных
$result = array();

$id_user = $_COOKIE["user"];
$id_hiking = isset($_POST['id_hiking'])?intval($_POST['id_hiking']):0;
$id_recipe = isset($_POST['id_recipe'])?intval($_POST['id_recipe']):0;
$comment = $mysqli->real_escape_string(trim($_POST['comment']));


if(!($id_hiking>0)){die(json_encode(array("error"=>"id_hiking is undefined")));}
$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_user} LIMIT 1");
if($q && $q->num_rows===0){
	$q = $mysqli->query("SELECT id FROM hiking_editors WHERE id_hiking={$id_hiking}  AND is_cook=1  AND id_user = {$id_user} LIMIT 1");
	if($q && $q->num_rows===0){
		die(json_encode(array("error"=>"Нет доступа")));
	}
}

if($mysqli->query("INSERT INTO `hiking_menu_exclude_recipes` SET `id_hiking`={$id_hiking},`id_recipe`={$id_recipe},`comment`='{$comment}',`id_user`={$id_user}, date=NOW()")){
	die(json_encode(array('success'=>true, 'id'=>$mysqli->insert_id)));
} else {
	die(json_encode(array('error'=>$mysqli->error)));
}