<?php
include("../../../blocks/db.php"); //подключение к БД
include("../../../blocks/for_auth.php"); //Только для авторизованных
$res = array();
$id_hiking = intval($_GET['id_hiking']);
$id_product = intval($_GET['id_product']);

$addwhere = "";

if(isset($_GET['id_act'])){$addwhere .= " AND hiking_menu.id_act=".intval($_GET['id_act'])." ";}
if(isset($_GET['date'])){$addwhere .= " AND hiking_menu.date='".$mysqli->real_escape_string($_GET['date'])."' ";}

$q = $mysqli->query("
					SELECT 
						food_acts.*,
						hiking_schedule.*,
						food_acts.name AS act_name,
						hiking_menu.*,
						recipes.*,
						COUNT(recipes.id) AS cou,
						
						recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100) AS amount,
						recipes.name AS name,
						recipes_products.id AS id_product,
						
						GROUP_CONCAT(CONCAT(users.id,'|',recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100),'|', hiking_menu.date)) AS uss,
						
						(SELECT
						GROUP_CONCAT(CONCAT(weight,'|',value,'|', cost))
						FROM recipes_products_units_values
						WHERE id_product=recipes_products.id GROUP BY recipes_products_units_values.id_product) AS cost
					FROM hiking_menu
						LEFT JOIN recipes ON recipes.id = hiking_menu.id_recipe 
						LEFT JOIN recipes_structure ON recipes_structure.id_recipe = recipes.id
						LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id
						LEFT JOIN hiking_schedule ON (hiking_schedule.id_food_act = hiking_menu.id_act AND hiking_menu.id_hiking =hiking_schedule.id_hiking AND DAY(hiking_schedule.d1)=DAY(hiking_menu.date))
						LEFT JOIN food_acts  ON hiking_menu.id_act = food_acts.id
						LEFT JOIN users ON hiking_menu.assignee_user = users.id
					WHERE hiking_menu.id_hiking={$id_hiking} AND recipes_products.id={$id_product} ".$addwhere." GROUP BY recipes.id");//
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
while($r = $q->fetch_assoc()){
	$res[] = $r;
}

exit(json_encode($res));
