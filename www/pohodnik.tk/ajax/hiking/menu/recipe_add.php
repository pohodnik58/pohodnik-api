<?php
include("../../../blocks/db.php");
include("../../../blocks/for_auth.php");
include("../../../blocks/err.php");
$result = array();

$id_user = $_COOKIE["user"];
$id_hiking = isset($_POST['id_hiking'])?intval($_POST['id_hiking']):0;
$id_recipe = isset($_POST['id_recipe'])?$_POST['id_recipe']:0;
$is_optimize = isset($_POST['is_optimize'])?intval($_POST['is_optimize']):0;

if(!($id_hiking>0)){die(json_encode(array("error"=>"id_hiking is undefined")));}
$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_user} LIMIT 1");
if($q && $q->num_rows===0){
	$q = $mysqli->query("SELECT id FROM hiking_editors WHERE id_hiking={$id_hiking}  AND is_cook=1  AND id_user = {$id_user} LIMIT 1");
	if($q && $q->num_rows===0){
		die(json_encode(array("error"=>"Нет доступа")));
	}
}

if(is_array($id_recipe)){

	$q = $mysqli->query("SELECT id_recipe FROM hiking_recipes WHERE `id_hiking`={$id_hiking} AND `id_recipe` IN(".implode(",", $id_recipe).")");
	if($q && $q->num_rows>0){ 
		$exsts = array();
		while($r = $q->fetch_row()){$exsts[]=$r[0];}
		$id_recipe = array_filter($id_recipe, function($k) {
		    return in_array($k,$exsts);
		});
	}	
	$values = array();
	foreach($id_recipe as $id){
		$values[]="({$id_hiking},{$id},{$is_optimize})";
	}
} else {

	$q = $mysqli->query("SELECT id FROM hiking_recipes WHERE `id_hiking`={$id_hiking} AND `id_recipe`={$id_recipe} LIMIT 1");
	if($q && $q->num_rows===1){ die(json_encode(array("error"=>"Уже добавлено"))); }	
	$values = array("({$id_hiking},{$id_recipe},{$is_optimize})");
}



if($mysqli->query("INSERT INTO `hiking_recipes`( `id_hiking`, `id_recipe`, `is_optimize`) VALUES ".implode(',',$values)."")){
	die(json_encode(array('success'=>true, 'id'=>$mysqli->insert_id)));
} else {
	die(json_encode(array('error'=>$mysqli->error)));
}