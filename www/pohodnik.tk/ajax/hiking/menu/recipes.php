<?php
include("../../../blocks/db.php");
include("../../../blocks/for_auth.php");
include("../../../blocks/err.php");
$result = array();
$id_user = $_COOKIE["user"];
$id_hiking = isset($_GET['id_hiking'])?intval($_GET['id_hiking']):0;

$q = $mysqli->query("
	SELECT recipes_categories.name AS category, hiking_recipes.*, recipes.* 
	FROM `hiking_recipes` 
	LEFT JOIN recipes ON hiking_recipes.id_recipe = recipes.id 
	LEFT JOIN recipes_categories ON recipes.id_category = recipes_categories.id 
	WHERE hiking_recipes.id_hiking={$id_hiking}
");
if(!$q){die(json_encode(array('error'=>$mysqli->error)));}
$res = array();
while($r = $q->fetch_assoc()){
	$res[] = $r;
}

die(json_encode($res));