<?php
include("../../../blocks/db.php");
include("../../../blocks/for_auth.php");
include("../../../blocks/err.php");
$result = array();

$id_user = $_COOKIE["user"];
$id_hiking = isset($_POST['id_hiking'])?intval($_POST['id_hiking']):0;
$id_recipe = isset($_POST['id_recipe'])?($_POST['id_recipe']):0;

if(!($id_hiking>0)){die(json_encode(array("error"=>"id_hiking is undefined")));}
$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_user} LIMIT 1");
if($q && $q->num_rows===0){
	$q = $mysqli->query("SELECT id FROM hiking_editors WHERE id_hiking={$id_hiking}  AND is_cook=1  AND id_user = {$id_user} LIMIT 1");
	if($q && $q->num_rows===0){
		die(json_encode(array("error"=>"Нет доступа")));
	}
}

if($mysqli->query("DELETE FROM `hiking_recipes` WHERE `id_hiking`={$id_hiking} 
				AND `id_recipe`".(is_array($id_recipe)?" IN(".implode(',',$id_recipe).")":"={$id_recipe}")."")){
	die(json_encode(array('success'=>true)));
} else {
	die(json_encode(array('error'=>$mysqli->error)));
}