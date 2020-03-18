<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
include("../../blocks/dates.php");
$result = array();

$id_user = $_COOKIE["user"];
$id_hiking = isset($_GET['id_hiking'])?intval($_GET['id_hiking']):0;
if(!($id_hiking>0)){die(json_encode(array("error"=>"id_hiking is undefined")));}
$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_user} LIMIT 1");
if($q && $q->num_rows===0){
	$q = $mysqli->query("SELECT id FROM hiking_editors WHERE id_hiking={$id_hiking}  AND is_cook=1  AND id_user = {$id_user} LIMIT 1");
	if($q && $q->num_rows===0){
		die(json_encode(array("error"=>"Нет доступа")));
	}
}
$res = array();
$q = $mysqli->query("SELECT hmer.id, hmer.id_recipe,hmer.comment, recipes.name, hmer.id_user, UNIX_TIMESTAMP(hmer.date) +{$time_offset} AS date FROM hiking_menu_exclude_recipes AS hmer LEFT JOIN recipes ON recipes.id=hmer.id_recipe WHERE hmer.id_hiking={$id_hiking}");
if($q){
	while($r=$q->fetch_assoc()){
		$res[] = $r;
	}
	die(json_encode($res));
} else {
	die(json_encode(array('error'=>$mysqli->error)));
}