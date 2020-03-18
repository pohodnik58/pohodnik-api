	<?php
	
	include("../../../blocks/db.php"); //подключение к БД
	$id_hiking = intval($_GET['id_hiking']);
	
	
	$where = " hiking_menu.id_hiking={$id_hiking} ";
	
	if(isset($_GET['id_user'])){
		$where .= " AND  hiking_menu.assignee_user=".intval($_GET['id_user']);
	}
	
	if(isset($_GET['date'])){
		$where .= " AND  hiking_menu.date='".intval($_GET['date'])."'";
	}	

	if(isset($_GET['id_product'])){
		$where .= " AND  IF(	
		hiking_menu.is_optimize=1,
		IFNULL(rp1.id,IFNULL(rp2.id, recipes_products.id)),
		recipes_products.id
	)=".intval($_GET['id_product']);
	}
	
	$q = $mysqli->query("	SELECT 
	IFNULL(forseuser.name,users.name) AS uname,
	IFNULL(forseuser.surname, users.surname) AS usurname,
	IFNULL(forseuser.id, users.id) AS uid,
	food_acts.name AS food_act_name,
	".(isset($_GET['id_product'])?'1':'0')." AS is_one,
	NOT ISNULL( forseuser.id ) AS is_force,
	if(
		hiking_menu.is_optimize=1,
		IF(LENGTH(recipes.name_opt)>0,recipes.name_opt, recipes.name),
		recipes.name
	) AS recipe_name,
	
	IF(	
		hiking_menu.is_optimize=1,
		IFNULL(rp1.name,IFNULL(rp2.name, recipes_products.name)),
		recipes_products.name
	) AS name,
	hiking_schedule.d1,
	 
		IF(	
			hiking_menu.is_optimize=1,
			IFNULL(recipes_structure_alt.amount*(hiking_menu.сorrection_coeff_pct/100),IFNULL(((recipes_structure.amount/100)*recipes_products_alt.weight)*(hiking_menu.сorrection_coeff_pct/100), recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100))) ,
			recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100)
		) AS amount,	
	
	IF(	
		hiking_menu.is_optimize=1,
		IFNULL(rp1.id,IFNULL(rp2.id, recipes_products.id)),
		recipes_products.id
	) AS id_product,
	

	UNIX_TIMESTAMP(hiking_schedule.d1) AS uts,
	
	
	hiking_menu.is_optimize,
	
	IF(	
		hiking_menu.is_optimize=1,
		IFNULL(
			(recipes_structure_alt.amount*(hiking_menu.сorrection_coeff_pct/100)),
			IFNULL(
				((recipes_structure.amount/100)*recipes_products_alt.weight)*(hiking_menu.сorrection_coeff_pct/100), 
				recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100)
			)
		)*(IFNULL(rp1.protein,IFNULL(rp2.protein, recipes_products.protein))/100),
		(recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100))*(recipes_products.protein/100)
	) AS protein,

	IF(	
		hiking_menu.is_optimize=1,
		IFNULL(
			(recipes_structure_alt.amount*(hiking_menu.сorrection_coeff_pct/100)),
			IFNULL(
				((recipes_structure.amount/100)*recipes_products_alt.weight)*(hiking_menu.сorrection_coeff_pct/100), 
				recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100)
			)
		)*(IFNULL(rp1.carbohydrates,IFNULL(rp2.carbohydrates, recipes_products.carbohydrates))/100),
		(recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100))*(recipes_products.carbohydrates/100)
	) AS carbohydrates,
	
	IF(	
		hiking_menu.is_optimize=1,
		IFNULL(
			(recipes_structure_alt.amount*(hiking_menu.сorrection_coeff_pct/100)),
			IFNULL(
				((recipes_structure.amount/100)*recipes_products_alt.weight)*(hiking_menu.сorrection_coeff_pct/100), 
				recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100)
			)
		)*(IFNULL(rp1.fat,IFNULL(rp2.fat, recipes_products.fat))/100),
		(recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100))*(recipes_products.fat/100)
	) AS fat,

	IF(	
		hiking_menu.is_optimize=1,
		IFNULL(
			(recipes_structure_alt.amount*(hiking_menu.сorrection_coeff_pct/100)),
			IFNULL(
				((recipes_structure.amount/100)*recipes_products_alt.weight)*(hiking_menu.сorrection_coeff_pct/100), 
				recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100)
			)
		)*(IFNULL(rp1.energy,IFNULL(rp2.energy, recipes_products.energy))/100),
		(recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100))*(recipes_products.energy/100)
	) AS energy
	
	
	
	
	
	
FROM hiking_menu
	LEFT JOIN recipes ON recipes.id = hiking_menu.id_recipe 
	LEFT JOIN recipes_structure ON recipes_structure.id_recipe = recipes.id
	LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id
	LEFT JOIN recipes_structure_alt ON recipes_structure_alt.id_rs = recipes_structure.id 
	LEFT JOIN recipes_products AS rp1 ON (recipes_structure_alt.id_product = rp1.id)
	LEFT JOIN recipes_products_alt ON recipes_products_alt.id_product = recipes_products.id 
	LEFT JOIN recipes_products AS rp2 ON recipes_products_alt.id_alt = rp2.id
	LEFT JOIN hiking_schedule ON (hiking_schedule.id_food_act = hiking_menu.id_act AND hiking_menu.id_hiking =hiking_schedule.id_hiking AND 			DAY(hiking_schedule.d1)=DAY(hiking_menu.date))
	LEFT JOIN food_acts  ON hiking_menu.id_act = food_acts.id
	LEFT JOIN users ON hiking_menu.assignee_user = users.id
	LEFT JOIN hiking_menu_products_force ON (hiking_menu.id_hiking = hiking_menu_products_force.id_hiking AND hiking_menu_products_force.id_product = IF(
		hiking_menu.is_optimize=1,
		IFNULL(rp1.id,IFNULL(rp2.id, recipes_products.id)),
		recipes_products.id
	))
	LEFT JOIN users AS forseuser ON forseuser.id = hiking_menu_products_force.id_user
	
WHERE {$where} ORDER BY hiking_schedule.d1, recipes.id");

if(!$q){die(json_encode(array("error"=>$mysqli->error)));}

$res = array();

while($r = $q->fetch_assoc()){
	$r['date_rus'] = date('d.m.y', $r['uts']);
	$r['time_rus'] = date('H:i', $r['uts']);
	$res[] = $r;
}

echo (json_encode($res));