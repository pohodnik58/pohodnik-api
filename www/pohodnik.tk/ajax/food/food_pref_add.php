<?php 
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_recipe = intval($_POST['id_recipe']);
$id_act = intval($_POST['id_act']);
$id_user = $_COOKIE["user"];

$q = $mysqli->query("SELECT id FROM user_food_pref WHERE id_user={$id_user} AND id_recipe={$id_recipe} AND id_act={$id_act} LIMIT 1");
if($q && $q->num_rows===1){die(json_encode(array("error"=>"Уже добавлен")));}

if($mysqli->query("INSERT INTO user_food_pref SET id_user={$id_user}, id_recipe={$id_recipe}, id_act={$id_act}")){
	die(json_encode(array("success"=>true, "id"=>$mysqli->insert_id)));
} else {
	die(json_encode(array("error"=>"Ошибка ".$mysqli->error)));
}