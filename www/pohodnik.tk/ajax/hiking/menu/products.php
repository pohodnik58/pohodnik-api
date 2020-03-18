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
						IF(	
							hiking_menu.is_optimize=1,
							IFNULL(rp1.name,IFNULL(rp2.name, recipes_products.name)),
							recipes_products.name
						) AS name,
						
						SUM( 
							IF(	
								hiking_menu.is_optimize=1,
								IFNULL(recipes_structure_alt.amount*(hiking_menu.сorrection_coeff_pct/100),IFNULL(((recipes_structure.amount/100)*recipes_products_alt.weight)*(hiking_menu.сorrection_coeff_pct/100), recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100))) ,
								recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100)
							)
						) AS amount,	
						
						IF(	
							hiking_menu.is_optimize=1,
							IFNULL(rp1.id,IFNULL(rp2.id, recipes_products.id)),
							recipes_products.id
						) AS id_product,
						
						GROUP_CONCAT(CONCAT(IF(hiking_menu.is_optimize=1,IF(LENGTH(recipes.name_opt)>0,recipes.name_opt,recipes.name ), recipes.name ),'|',hiking_menu.date,'|',IF(	
							hiking_menu.is_optimize=1,
							IFNULL(recipes_structure_alt.amount*(hiking_menu.сorrection_coeff_pct/100),IFNULL(((recipes_structure.amount/100)*recipes_products_alt.weight)*(hiking_menu.сorrection_coeff_pct/100), recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100))) ,
							recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100)
						))) AS use9,
						
						hiking_menu.is_optimize,
						(SELECT GROUP_CONCAT(CONCAT(weight,'|',value,'|', cost)) FROM recipes_products_units_values WHERE id_product=IFNULL(rp1.id,IFNULL(rp2.id, recipes_products.id)) GROUP BY recipes_products_units_values.id_product) AS cost
					FROM hiking_menu
						LEFT JOIN recipes ON recipes.id = hiking_menu.id_recipe 
						LEFT JOIN recipes_structure ON recipes_structure.id_recipe = recipes.id
						LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id
						LEFT JOIN recipes_structure_alt ON recipes_structure_alt.id_rs = recipes_structure.id 
						LEFT JOIN recipes_products AS rp1 ON (recipes_structure_alt.id_product = rp1.id)
						LEFT JOIN recipes_products_alt ON recipes_products_alt.id_product = recipes_products.id 
						LEFT JOIN recipes_products AS rp2 ON recipes_products_alt.id_alt = rp2.id
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
