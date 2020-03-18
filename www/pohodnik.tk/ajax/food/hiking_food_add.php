<?php 
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_recipe = intval($_POST['id_recipe']);
$id_act    = intval($_POST['id_act']);
$id_hiking = intval($_POST['id_hiking']);

if(!($id_hiking>0)){die(json_encode(array("error"=>"id_hiking is undefined")));}
$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_user} LIMIT 1");
if($q && $q->num_rows===0){
	$q = $mysqli->query("SELECT id FROM hiking_editors WHERE id_hiking={$id_hiking}  AND is_cook=1  AND id_user = {$id_user} LIMIT 1");
	if($q && $q->num_rows===0){
		die(json_encode(array("error"=>"Нет доступа")));
	}
}

$q = $mysqli->query("SELECT id FROM hiking_food_list WHERE id_hiking={$id_hiking} AND id_recipe={$id_recipe} AND id_act={$id_act} LIMIT 1");
if($q && $q->num_rows===1){die(json_encode(array("error"=>"Уже добавлен")));}

if($mysqli->query("INSERT INTO hiking_food_list SET id_hiking={$id_hiking}, id_recipe={$id_recipe}, id_act={$id_act}")){
	die(json_encode(array("success"=>true, "id"=>$mysqli->insert_id)));
} else {
	die(json_encode(array("error"=>"Ошибка ".$mysqli->error)));
}