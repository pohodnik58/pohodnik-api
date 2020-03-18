<?php
include("../../../blocks/db.php"); //подключение к БД
include("../../../blocks/for_auth.php"); //Только для авторизованных
$res = array();
$id_hiking = intval($_GET['id_hiking']);

$addwhere = "";

if(isset($_GET['id_act'])){$addwhere .= " AND hiking_menu.id_act=".intval($_GET['id_act'])." ";}
if(isset($_GET['date'])){$addwhere .= " AND hiking_menu.date='".$mysqli->real_escape_string($_GET['date'])."' ";}
/*
	SELECT 
		recipes.name,
		hiking_menu.*,
		SUM((recipes_products.protein/100)*recipes_structure.amount) AS protein,
		SUM((recipes_products.fat/100)*recipes_structure.amount) AS fat,
		SUM((recipes_products.carbohydrates/100)*recipes_structure.amount) AS carbohydrates,
		SUM((recipes_products.energy/100)*recipes_structure.amount) AS energy,
		SUM(recipes_structure.amount) AS amount,

		SUM((IFNULL(rp1.protein,IFNULL(rp2.protein, recipes_products.protein))/100)*IFNULL(recipes_structure_alt.amount, IFNULL((recipes_structure.amount/100)*recipes_products_alt.weight, recipes_structure.amount))
		)  AS optimize_protein,
		
		SUM( (IFNULL(rp1.fat,IFNULL(rp2.fat, recipes_products.fat))/100)*IFNULL(recipes_structure_alt.amount, IFNULL((recipes_structure.amount/100)*recipes_products_alt.weight, recipes_structure.amount))
		)  AS optimize_fat,

		SUM( (IFNULL(rp1.carbohydrates,IFNULL(rp2.carbohydrates, recipes_products.carbohydrates))/100)*IFNULL(recipes_structure_alt.amount, IFNULL((recipes_structure.amount/100)*recipes_products_alt.weight, recipes_structure.amount)) ) AS optimize_carbohydrates,
		SUM((IFNULL(rp1.energy,IFNULL(rp2.energy, recipes_products.energy))/100)*IFNULL(recipes_structure_alt.amount, IFNULL((recipes_structure.amount/100)*recipes_products_alt.weight, recipes_structure.amount)) ) AS optimize_energy,
		SUM( IFNULL(recipes_structure_alt.amount,IFNULL((recipes_structure.amount/100)*recipes_products_alt.weight, recipes_structure.amount)) ) AS optimize_amount
		
		
	FROM hiking_menu
		LEFT JOIN recipes ON recipes.id = hiking_menu.id_recipe 
		
		LEFT JOIN recipes_structure ON recipes_structure.id_recipe = recipes.id
		LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id
		
		LEFT JOIN recipes_structure_alt ON recipes_structure_alt.id_rs = recipes_structure.id 
		LEFT JOIN recipes_products AS rp1 ON recipes_structure_alt.id_product = rp1.id
		
		LEFT JOIN recipes_products_alt ON recipes_products_alt.id_product = recipes_products.id 
		LEFT JOIN recipes_products AS rp2 ON recipes_products_alt.id_alt = rp2.id
		
		
	WHERE hiking_menu.id_hiking={$id_hiking} ".$addwhere." GROUP BY  hiking_menu.id_recipe, hiking_menu.id_act, hiking_menu.date

*/
$q = $mysqli->query("
					SELECT 
						recipes.*,
						hiking_menu.*,
						CONCAT(users.name,' ',users.surname) AS uname,
						users.id AS uid,
						SUM((recipes_products.protein/100)*recipes_structure.amount) AS protein,
						SUM((recipes_products.fat/100)*recipes_structure.amount) AS fat,
						SUM((recipes_products.carbohydrates/100)*recipes_structure.amount) AS carbohydrates,
						SUM((recipes_products.energy/100)*recipes_structure.amount) AS energy,
						SUM(recipes_structure.amount) AS amount,

						(	SELECT 
								SUM(
									(IFNULL(rp1.protein,IFNULL(rp2.protein, recipes_products.protein))/100)*IFNULL(recipes_structure_alt.amount, IFNULL((recipes_structure.amount/100)*recipes_products_alt.weight, recipes_structure.amount))
								) 
							FROM recipes_structure 
								LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id  
								LEFT JOIN recipes_structure_alt ON recipes_structure_alt.id_rs = recipes_structure.id 
								LEFT JOIN recipes_products AS rp1 ON recipes_structure_alt.id_product = rp1.id
								LEFT JOIN recipes_products_alt ON recipes_products_alt.id_product = recipes_products.id 
								LEFT JOIN recipes_products AS rp2 ON recipes_products_alt.id_alt = rp2.id
							WHERE recipes_structure.id_recipe = recipes.id  
							ORDER BY recipes_structure_alt.amount, recipes_products_alt.weight
							LIMIT 1
						) AS optimize_protein,

						(	SELECT 
								SUM(
									(IFNULL(rp1.fat,IFNULL(rp2.fat, recipes_products.fat))/100)*IFNULL(recipes_structure_alt.amount, IFNULL((recipes_structure.amount/100)*recipes_products_alt.weight, recipes_structure.amount))
								) 
							FROM recipes_structure 
								LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id  
								LEFT JOIN recipes_structure_alt ON recipes_structure_alt.id_rs = recipes_structure.id 
								LEFT JOIN recipes_products AS rp1 ON recipes_structure_alt.id_product = rp1.id
								LEFT JOIN recipes_products_alt ON recipes_products_alt.id_product = recipes_products.id 
								LEFT JOIN recipes_products AS rp2 ON recipes_products_alt.id_alt = rp2.id
							WHERE recipes_structure.id_recipe = recipes.id  
							ORDER BY recipes_structure_alt.amount, recipes_products_alt.weight
							LIMIT 1
						) AS optimize_fat,

						(	SELECT 
								SUM(
									(IFNULL(rp1.carbohydrates,IFNULL(rp2.carbohydrates, recipes_products.carbohydrates))/100)*IFNULL(recipes_structure_alt.amount, IFNULL((recipes_structure.amount/100)*recipes_products_alt.weight, recipes_structure.amount))
								) 
							FROM recipes_structure 
								LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id  
								LEFT JOIN recipes_structure_alt ON recipes_structure_alt.id_rs = recipes_structure.id 
								LEFT JOIN recipes_products AS rp1 ON recipes_structure_alt.id_product = rp1.id
								LEFT JOIN recipes_products_alt ON recipes_products_alt.id_product = recipes_products.id 
								LEFT JOIN recipes_products AS rp2 ON recipes_products_alt.id_alt = rp2.id
							WHERE recipes_structure.id_recipe = recipes.id  
							ORDER BY recipes_structure_alt.amount, recipes_products_alt.weight
							LIMIT 1
						) AS optimize_carbohydrates,

						(	SELECT 
								SUM(
									(IFNULL(rp1.energy,IFNULL(rp2.energy, recipes_products.energy))/100)*IFNULL(recipes_structure_alt.amount, IFNULL((recipes_structure.amount/100)*recipes_products_alt.weight, recipes_structure.amount))
								) 
							FROM recipes_structure 
								LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id  
								LEFT JOIN recipes_structure_alt ON recipes_structure_alt.id_rs = recipes_structure.id 
								LEFT JOIN recipes_products AS rp1 ON recipes_structure_alt.id_product = rp1.id
								LEFT JOIN recipes_products_alt ON recipes_products_alt.id_product = recipes_products.id 
								LEFT JOIN recipes_products AS rp2 ON recipes_products_alt.id_alt = rp2.id
							WHERE recipes_structure.id_recipe = recipes.id  
							ORDER BY recipes_structure_alt.amount, recipes_products_alt.weight
							LIMIT 1
						) AS optimize_energy,
						
						(	SELECT 
								SUM(
									IFNULL(recipes_structure_alt.amount,IFNULL((recipes_structure.amount/100)*recipes_products_alt.weight, recipes_structure.amount))
								) 
							FROM recipes_structure 
								LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id  
								LEFT JOIN recipes_structure_alt ON recipes_structure_alt.id_rs = recipes_structure.id 
								LEFT JOIN recipes_products AS rp1 ON recipes_structure_alt.id_product = rp1.id
								LEFT JOIN recipes_products_alt ON recipes_products_alt.id_product = recipes_products.id 
								LEFT JOIN recipes_products AS rp2 ON recipes_products_alt.id_alt = rp2.id
							WHERE recipes_structure.id_recipe = recipes.id  
							ORDER BY recipes_structure_alt.amount, recipes_products_alt.weight
							LIMIT 1
						) AS optimize_amount
					FROM hiking_menu
						LEFT JOIN recipes ON recipes.id = hiking_menu.id_recipe 
						LEFT JOIN recipes_structure ON recipes_structure.id_recipe = recipes.id
						LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id
						LEFT JOIN users ON  hiking_menu.assignee_user = users.id
						
					WHERE hiking_menu.id_hiking={$id_hiking} ".$addwhere." GROUP BY  hiking_menu.id_recipe, hiking_menu.id_act, hiking_menu.date
				");//
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
while($r = $q->fetch_assoc()){
	$res[] = $r;
}

exit(json_encode($res));
