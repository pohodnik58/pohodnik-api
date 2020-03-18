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
						
						if(
							hiking_menu.is_optimize=1,
							(IFNULL(recipes_structure_alt.amount*(hiking_menu.сorrection_coeff_pct/100),IFNULL(((recipes_structure.amount/100)*recipes_products_alt.weight)*(hiking_menu.сorrection_coeff_pct/100), recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100))) ),
							recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100)
						) AS amount,
						
						if(
							hiking_menu.is_optimize=1,
							IF(LENGTH(recipes.name_opt)>0,recipes.name_opt, recipes.name),
							recipes.name
						) AS name,					
						
						IFNULL(rp1.id,IFNULL(rp2.id, recipes_products.id)) AS id_product,
						
						GROUP_CONCAT(CONCAT(users.id,'|',if(
							hiking_menu.is_optimize=1,
							(IFNULL(recipes_structure_alt.amount*(hiking_menu.сorrection_coeff_pct/100),IFNULL(((recipes_structure.amount/100)*recipes_products_alt.weight)*(hiking_menu.сorrection_coeff_pct/100), recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100))) ),
							recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100)
						),'|', hiking_menu.date)) AS uss,
						
						(SELECT GROUP_CONCAT(CONCAT(weight,'|',value,'|', cost)) FROM recipes_products_units_values WHERE id_product=IFNULL(rp1.id,IFNULL(rp2.id, recipes_products.id)) GROUP BY recipes_products_units_values.id_product) AS cost
					FROM hiking_menu
						LEFT JOIN recipes ON recipes.id = hiking_menu.id_recipe 
						LEFT JOIN recipes_structure ON recipes_structure.id_recipe = recipes.id
						LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id
						LEFT JOIN recipes_structure_alt ON recipes_structure_alt.id_rs = recipes_structure.id 
						LEFT JOIN recipes_products AS rp1 ON (recipes_structure_alt.id_product = rp1.id)
						LEFT JOIN recipes_products_alt ON recipes_products_alt.id_product = recipes_products.id 
						LEFT JOIN recipes_products AS rp2 ON recipes_products_alt.id_alt = rp2.id
						LEFT JOIN hiking_schedule ON (hiking_schedule.id_food_act = hiking_menu.id_act AND hiking_menu.id_hiking =hiking_schedule.id_hiking AND DAY(hiking_schedule.d1)=DAY(hiking_menu.date))
						LEFT JOIN food_acts  ON hiking_menu.id_act = food_acts.id
						LEFT JOIN users ON hiking_menu.assignee_user = users.id
					WHERE hiking_menu.id_hiking={$id_hiking} AND IFNULL(rp1.id,IFNULL(rp2.id, recipes_products.id))={$id_product} ".$addwhere." GROUP BY recipes.id");//
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
while($r = $q->fetch_assoc()){
	$res[] = $r;
}

exit(json_encode($res));
