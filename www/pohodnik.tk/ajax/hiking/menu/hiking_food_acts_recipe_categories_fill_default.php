<?php
include("../../../blocks/db.php");
include("../../../blocks/for_auth.php");
include("../../../blocks/err.php");
include("../../../blocks/global.php");
$result = array();
$id_user = $_COOKIE["user"];
$id_hiking = isset($_POST['id_hiking'])?intval($_POST['id_hiking']):0;
if(!($id_hiking>0)){die(json_encode(array("error"=>"id_hiking is undefined")));}
$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_user} LIMIT 1");
if($q && $q->num_rows===0){
	$q = $mysqli->query("SELECT id FROM hiking_editors WHERE id_hiking={$id_hiking}  AND is_cook=1  AND id_user = {$id_user} LIMIT 1");
	if($q && $q->num_rows===0){
		die(json_encode(array("error"=>"Нет доступа")));
	}
}


$q = $mysqli->query("DELETE FROM hiking_food_acts_recipe_categories WHERE id_hiking={$id_hiking}");
if(!$q) {
    die(err($mysqli->error, 'clear'));
}

$q = $mysqli->query("INSERT INTO `hiking_food_acts_recipe_categories`(`id_hiking`, `id_food_acts`, `id_recipe_category`, `can_increase`, `can_dublicate`, `min_pct`, `max_pct`, `order_index`) SELECT {$id_hiking} AS `id_hiking`, `id_food_acts`, `id_recipe_category`, `can_increase`, `can_dublicate`, `min_pct`, `max_pct`, `order_index` FROM `food_acts_recipe_categories` WHERE 1");
if(!$q) {
    die(err($mysqli->error));
}

die(out(array("success" => true, "affected" => $mysqli->affected_rows)));