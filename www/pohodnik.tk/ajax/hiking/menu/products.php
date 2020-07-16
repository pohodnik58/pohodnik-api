<?php
include("../../../blocks/db.php"); //подключение к БД
include("../../../blocks/for_auth.php"); //Только для авторизованных
$res = array();
$id_hiking = intval($_GET['id_hiking']);

$addwhere = "";

if(isset($_GET['id_act'])){$addwhere .= " AND hiking_menu.id_act=".intval($_GET['id_act'])." ";}
if(isset($_GET['date'])){$addwhere .= " AND hiking_menu.date='".$mysqli->real_escape_string($_GET['date'])."' ";}

$q = $mysqli->query("
SELECT
recipes_products.name,
SUM(recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100)) AS amount,	
recipes_products.id AS id_product,

GROUP_CONCAT(
	CONCAT(
		recipes.name,
		'|',
		hiking_menu.date,
		'|',
		recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100)
	)
) AS use9,

0 AS is_optimize,
(SELECT GROUP_CONCAT(CONCAT(weight,'|',value,'|', cost)) 
FROM recipes_products_units_values WHERE id_product=recipes_products.id GROUP BY recipes_products_units_values.id_product) AS cost
FROM hiking_menu
LEFT JOIN recipes ON recipes.id = hiking_menu.id_recipe 
LEFT JOIN recipes_structure ON recipes_structure.id_recipe = recipes.id
LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id
WHERE hiking_menu.id_hiking={$id_hiking} ".$addwhere." GROUP BY id_product");//
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
while($r = $q->fetch_assoc()){
	$res[] = $r;
}
if(!isset($_GET['view'])){
exit(json_encode($res));
} else {
	echo '<html><head><meta charset="utf-8"></head><body><table border="1" cellspacing=0 cellpadding=3>';
	echo '<tr>';
		foreach($res[0] as $k=>$v){
			echo '<td>'.$k.'</td>';
		}
	echo '</tr>';
	
	
	foreach($res as $r){
		foreach($r as $k=>$v){
			echo '<tr>';
				foreach($r as $k=>$v){
					echo '<td>'.$v.'</td>';
				}
			echo '</tr>'; 
		}
	}
	echo '</table>';
}
