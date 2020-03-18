<?php //recipes_product_add.php

include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
include("../../blocks/dates.php");
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
$result = array();
$id = intval($_GET['id']);
$ext = isset($_GET['ext'])?true:false;
$optimize =  false;
if(isset($_GET['optimize'])){$optimize=$_GET['optimize'];}
$id_user = $_COOKIE["user"];

if(!isset($_POST['confirm'])){
$q = $mysqli->query("SELECT users.name, users.surname, hiking.id_author, hiking.confirm_list_products, UNIX_TIMESTAMP(hiking.confirm_list_date)+{$time_offset} AS dt, hiking.confirm_list_user FROM hiking LEFT JOIN users on users.id = hiking.confirm_list_user WHERE LENGTH(hiking.confirm_list_products)>5 AND hiking.id={$id} LIMIT 1");
if($q && $q->num_rows===1){
	$r = $q->fetch_assoc();
	

	$res = json_decode($r['confirm_list_products'], true);
	$res['confirm'] = array(
		"user"=> $r['name']." ".$r['surname'],
		"date"=> date('d.m.Y',$r['dt']),
		"time"=> date('H:i', $r['dt'])
	);
	$res["can_confirm"] = $r['id_author']===$id_user;
	$res["current_user"] = $id_user;
	
	exit(json_encode($res));
}
}


$q = $mysqli->query("SELECT `id_type`, `name`, id_author, UNIX_TIMESTAMP(`start`)+{$time_offset} AS start, UNIX_TIMESTAMP(`finish`)+{$time_offset} AS finish, menu_optimize FROM `hiking` WHERE id={$id} LIMIT 1");
if(!$q){ die(json_encode(array("error"=>$mysqli->error))); }
$hiking = $q->fetch_assoc();


	if(strlen($hiking['menu_optimize'])>0){
		$optimize=$hiking['menu_optimize'];
	}
	$result["optimize"] = $optimize;


$result["can_confirm"] = $hiking['id_author']===$id_user;

$hiking['start_date'] = date('d.m.Y H:i:s',$hiking['start']);
$hiking['finish_date'] = date('d.m.Y H:i:s',$hiking['finish']);
$equip_weight = 0;
$hiking['hikers'] = array();
$q = $mysqli->query("SELECT  hiking_members.id_user, UNIX_TIMESTAMP(hiking_members.date) AS date , users.name, users.surname,  users.vk_id, users.photo_50, users.sex
								FROM hiking_members LEFT JOIN users ON hiking_members.id_user = users.id
								WHERE hiking_members.id_hiking={$id} ORDER BY hiking_members.date DESC");
							
while($r = $q->fetch_assoc()){
	$r['equipment'] = array();
	$r['weight'] = 0;
	$r['products'] = array();
	$r['my'] = (intval($r['id_user']) === intval($id_user));
	$qe = $mysqli->query("SELECT user_equip.id, user_equip.name, user_equip.weight/1000 AS weight, user_equip.value FROM `hiking_equipment` LEFT JOIN user_equip ON user_equip.id=hiking_equipment.id_equip WHERE hiking_equipment.id_hiking = {$id} AND hiking_equipment.id_user=".$r['id_user']." AND hiking_equipment.is_confirm=1");
	if($qe && $qe->num_rows>0){
		
		
		while($re = $qe->fetch_assoc()){ 
			$r['equipment'][] = $re;
			$r['weight'] += $re['weight']*1000;
			$equip_weight += $re['weight']*1000;
		}
	}
	$hiking['hikers'][] = $r;		
}

$result['current_user'] = $id_user;





$acts = array();
$menus = array();
$q = $mysqli->query("SELECT hiking_schedule.name AS title, UNIX_TIMESTAMP(hiking_schedule.d1)+{$time_offset} AS d1, UNIX_TIMESTAMP(hiking_schedule.d1)+{$time_offset} AS d2, food_acts.norm_kkal, food_acts.id, food_acts.name  FROM `hiking_schedule` LEFT JOIN food_acts ON food_acts.id=hiking_schedule.id_food_act WHERE hiking_schedule.id_hiking={$id} AND hiking_schedule.id_food_act IS NOT NULL");
if($q && $q->num_rows>0){
	while($r = $q->fetch_assoc()){
		$menus[] = array("act"=>array('id'=> $r['id'], 'norm_kkal'=>$r['norm_kkal'], 'name'=>$r['name'], 'time'=>date('H:i', $r['d1'])), "date"=> $r['d1'], "date_rus"=>date('d.m.Y H:i:s',$r['d1']), "day_rus"=>date('d.m.Y',$r['d1']), "day"=>date('Y-m-d',$r['d1']));		
	}
} else {
	$acts = array();
	$q = $mysqli->query("SELECT `id`, `name`, norm_kkal, `time`, `coeff_pct` FROM `food_acts` WHERE 1");
	while($r = $q->fetch_assoc()){
		$acts[] = $r;		
	}

	$menus = array();
	$date = $hiking['start'];
	while($date<=$hiking['finish']){
		foreach($acts as $act){
			if(date('H:i:s',$date)===$act['time']){
				$menus[] = array("act"=>$act, "date"=> $date, "date_rus"=>date('d.m.Y H:i:s',$date), "day_rus"=>date('d.m.Y',$date), "day"=>date('Y-m-d',$date));
			}
		}
		$date= $date+ 1800;
	}
}






$result['acts'] = $menus;

$q = $mysqli->query("SELECT id_user FROM hiking_members WHERE id_hiking=".$id."");
if( $q && $q->num_rows===0 ){die(json_encode(array("error"=>"Нет участников")));}
$pref = array();
$users_ids = array();
while($r = $q->fetch_assoc()){$users_ids[] = $r['id_user'];}


if(!isset($_GET['man'])){

if($optimize){
	$z = "SELECT 
		COUNT( DISTINCT user_food_pref.id_user) AS cou, 
		user_food_pref.id_act,
		recipes.id,
		recipes.name,
		user_food_pref.id AS id_food_pref,
		recipes_categories.id AS category_id,
		recipes_categories.name AS category_name,
		(SELECT SUM((IFNULL(rp1.protein,recipes_products.protein)/100)*IFNULL(recipes_structure_alt.amount,recipes_structure.amount)) FROM recipes_structure LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id  LEFT JOIN recipes_structure_alt ON recipes_structure_alt.id_rs = recipes_structure.id LEFT JOIN recipes_products AS rp1 ON recipes_structure_alt.id_product = rp1.id WHERE recipes_structure.id_recipe = recipes.id) AS protein,
		(SELECT SUM((IFNULL(rp1.fat,recipes_products.fat)/100)*IFNULL(recipes_structure_alt.amount,recipes_structure.amount)) FROM recipes_structure LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id  LEFT JOIN recipes_structure_alt ON recipes_structure_alt.id_rs = recipes_structure.id LEFT JOIN recipes_products AS rp1 ON recipes_structure_alt.id_product = rp1.id WHERE recipes_structure.id_recipe = recipes.id) AS fat,
		(SELECT SUM((IFNULL(rp1.carbohydrates,recipes_products.carbohydrates)/100)*IFNULL(recipes_structure_alt.amount,recipes_structure.amount)) FROM recipes_structure LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id  LEFT JOIN recipes_structure_alt ON recipes_structure_alt.id_rs = recipes_structure.id LEFT JOIN recipes_products AS rp1 ON recipes_structure_alt.id_product = rp1.id  WHERE recipes_structure.id_recipe = recipes.id) AS carbohydrates,
		(SELECT SUM((IFNULL(rp1.energy,recipes_products.energy)/100)*IFNULL(recipes_structure_alt.amount,recipes_structure.amount)) FROM recipes_structure LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id  LEFT JOIN recipes_structure_alt ON recipes_structure_alt.id_rs = recipes_structure.id LEFT JOIN recipes_products AS rp1 ON recipes_structure_alt.id_product = rp1.id  WHERE recipes_structure.id_recipe = recipes.id) AS energy,
		(SELECT SUM(IFNULL(recipes_structure_alt.amount,recipes_structure.amount)) FROM recipes_structure LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id LEFT JOIN recipes_structure_alt ON recipes_structure_alt.id_rs = recipes_structure.id LEFT JOIN recipes_products AS rp1 ON recipes_structure_alt.id_product = rp1.id WHERE recipes_structure.id_recipe = recipes.id) AS amount
	FROM recipes 
		LEFT JOIN user_food_pref ON user_food_pref.id_recipe=recipes.id 
		LEFT JOIN recipes_categories ON recipes.id_category = recipes_categories.id
		LEFT JOIN food_acts_recipe_categories ON food_acts_recipe_categories.id_recipe_category = recipes_categories.id 
	WHERE user_food_pref.id_user IN (".implode(',', $users_ids).")  AND recipes.id NOT IN (SELECT id_recipe FROM hiking_menu_exclude_recipes  WHERE id_hiking ={$id}) GROUP BY    user_food_pref.id_act, recipes.id ORDER BY cou DESC,user_food_pref.id_act, food_acts_recipe_categories.id,recipes_categories.id, energy";
} else {
	$z = "SELECT 
		COUNT( DISTINCT user_food_pref.id_user) AS cou, 
		user_food_pref.id_act,
		recipes.id,
		recipes.name,
		user_food_pref.id AS id_food_pref,
		recipes_categories.id AS category_id,
		recipes_categories.name AS category_name,
		(SELECT SUM((recipes_products.protein/100)*recipes_structure.amount) FROM recipes_structure LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id WHERE recipes_structure.id_recipe = recipes.id) AS protein,
		(SELECT SUM((recipes_products.fat/100)*recipes_structure.amount) FROM recipes_structure LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id WHERE recipes_structure.id_recipe = recipes.id) AS fat,
		(SELECT SUM((recipes_products.carbohydrates/100)*recipes_structure.amount) FROM recipes_structure LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id WHERE recipes_structure.id_recipe = recipes.id) AS carbohydrates,
		(SELECT SUM((recipes_products.energy/100)*recipes_structure.amount) FROM recipes_structure LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id WHERE recipes_structure.id_recipe = recipes.id) AS energy,
		(SELECT SUM(recipes_structure.amount) FROM recipes_structure LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id WHERE recipes_structure.id_recipe = recipes.id) AS amount
	FROM recipes 
		LEFT JOIN user_food_pref ON user_food_pref.id_recipe=recipes.id 
		LEFT JOIN recipes_categories ON recipes.id_category = recipes_categories.id
		LEFT JOIN food_acts_recipe_categories ON food_acts_recipe_categories.id_recipe_category = recipes_categories.id 
	WHERE user_food_pref.id_user IN (".implode(',', $users_ids).")  AND recipes.id NOT IN (SELECT id_recipe FROM hiking_menu_exclude_recipes  WHERE id_hiking ={$id}) GROUP BY    user_food_pref.id_act, recipes.id ORDER BY cou DESC,user_food_pref.id_act, food_acts_recipe_categories.id,recipes_categories.id, energy";
}

$q = $mysqli->query($z);
if(!$q){die($mysqli->error);}
if($q->num_rows===0){die($z);}
while($r=$q->fetch_assoc()){


	if( !isset( $pref[$r['id_act']]) ){$pref[$r['id_act']] = array();}
	if( !isset($pref[$r['id_act']][$r['category_id']]) ){$pref[$r['id_act']][$r['category_id']]=array();}
	$pref[$r['id_act']][$r['category_id']][] = $r;
}
$result['pref'] = $pref;



$res = array();
$used = array();
$paylist_counts = array();
$used_dishes = array();



foreach($menus as $menu){
	$act = $menu['act']['id'];
	$target_kkal = intval($menu['act']['norm_kkal']);
	$kkal_fault = $target_kkal*0.1;
	$kkal_cur = 0;
	$menu['dishes'] = array();
	if(!isset($pref[$act])){continue;}
	foreach($pref[$act] as $id_cat=>$dish_incat){
		$incat = true;
		$cur_command_select = 10;
		for($i=0; $i<count($dish_incat); $i++){
			//$dish = $dish_incat[$i];	
			
			
			$dish = $dish_incat[$i];		
			if($incat  && $kkal_cur<($target_kkal+$kkal_fault) && ( $kkal_cur+$dish['energy']<($target_kkal+$kkal_fault) )){//

				if($ext){
					$dish['products'] = array();
			
						if($optimize){
							$qp = $mysqli->query("SELECT 
								recipes_products.id AS id_product, 
								IFNULL(rp1.id, recipes_products.id) AS id_product, 
								IFNULL(rp1.name, recipes_products.name) AS name, 
								IFNULL(rp1.protein, recipes_products.protein)/100*IFNULL(recipes_structure_alt.amount,recipes_structure.amount) AS protein, 
								IFNULL(rp1.fat, recipes_products.fat)/100*IFNULL(recipes_structure_alt.amount,recipes_structure.amount) AS fat, 
								IFNULL(rp1.carbohydrates, recipes_products.carbohydrates)/100*IFNULL(recipes_structure_alt.amount,recipes_structure.amount) AS carbohydrates, 
								IFNULL(rp1.energy, recipes_products.energy)/100*IFNULL(recipes_structure_alt.amount,recipes_structure.amount) AS energy, 
								recipes_products.weight, 
								recipes_products.cost, 
								recipes_structure.id,
								IFNULL(recipes_structure_alt.amount,recipes_structure.amount) AS amount
							FROM recipes_structure LEFT JOIN recipes_products ON recipes_products.id = recipes_structure.id_product 
							LEFT JOIN recipes_structure_alt ON recipes_structure_alt.id_rs = recipes_structure.id
							LEFT JOIN recipes_products AS rp1 ON rp1.id = recipes_structure_alt.id_product 
							WHERE recipes_structure.id_recipe=".$dish['id']." ORDER BY recipes_structure.amount DESC");
							
						} else {
							$qp = $mysqli->query("SELECT 
								recipes_products.id AS id_product, 
								recipes_products.name, 
								recipes_products.protein/100*recipes_structure.amount AS protein, 
								recipes_products.fat/100*recipes_structure.amount AS fat, 
								recipes_products.carbohydrates/100*recipes_structure.amount AS carbohydrates, 
								recipes_products.energy/100*recipes_structure.amount AS energy, 
								recipes_products.weight, 
								recipes_products.cost, 
								recipes_structure.id,
								recipes_structure.amount
							FROM recipes_structure LEFT JOIN recipes_products ON recipes_products.id = recipes_structure.id_product 
							WHERE recipes_structure.id_recipe=".$dish['id']." ORDER BY recipes_structure.amount DESC");
						}
						while($rp = $qp->fetch_assoc()){

							$dish['products'][] = $rp;
							
						}
					
				
				}
				
				$used_dishes[] = $dish['id'];
				
				$menu['dishes'][] = $dish;
				if(!isset($paylist_counts[$dish['id']])){ $paylist_counts[$dish['id']] = 0;}
				$paylist_counts[$dish['id']]++;

				if( count($pref[$act][$id_cat])>0 ){
					$pref[$act][$id_cat][] = array_shift($pref[$act][$id_cat]);///;
				}
				$kkal_cur+= $dish['energy'] or 0;
				$incat = false;
			}
		}

		$menu['energy'] = $kkal_cur;
		$menu['energy_target'] = $target_kkal;
	}
	
	
	
	
	
	$res[] = $menu;
}

$result['menu'] = $res;
//$result['paylist_counts'] = $paylist_counts;




$recs_id = array();
foreach($paylist_counts as $k=>$v){
	$recs_id[] = $k;
}
} else { //if($_GET['man']))


	$dishes = array();
	
	$q =  $mysqli->query("
		SELECT 
			1 AS cou, 
			hiking_menu.id_act,
			hiking_menu.date,
			hiking_menu.id AS id_hiking_menu,
			recipes.id,
			IF(
				hiking_menu.is_optimize=1,
				IF(LENGTH(recipes.name_opt)>0,recipes.name_opt,recipes.name),
				recipes.name
			) AS name,
			recipes_categories.id AS category_id,
			recipes_categories.name AS category_name,
			hiking_menu.is_optimize,
			
			SUM(IF(
				hiking_menu.is_optimize=1,
				((IFNULL(rp1.protein,IFNULL(rp2.protein, recipes_products.protein))/100)*IFNULL(recipes_structure_alt.amount, IFNULL((recipes_structure.amount/100)*recipes_products_alt.weight, recipes_structure.amount))),
				((recipes_products.protein/100)*recipes_structure.amount)
			)*(hiking_menu.сorrection_coeff_pct/100)) AS protein,
			
			SUM(IF(
				hiking_menu.is_optimize=1,
				( (IFNULL(rp1.fat,IFNULL(rp2.fat, recipes_products.fat))/100)*IFNULL(recipes_structure_alt.amount, IFNULL((recipes_structure.amount/100)*recipes_products_alt.weight, recipes_structure.amount))),
				((recipes_products.fat/100)*recipes_structure.amount)
			)*(hiking_menu.сorrection_coeff_pct/100)) AS fat,		
			
			SUM(IF(
				hiking_menu.is_optimize=1,
				( (IFNULL(rp1.carbohydrates,IFNULL(rp2.carbohydrates, recipes_products.carbohydrates))/100)*IFNULL(recipes_structure_alt.amount, IFNULL((recipes_structure.amount/100)*recipes_products_alt.weight, recipes_structure.amount)) ),
				((recipes_products.carbohydrates/100)*recipes_structure.amount)
			)*(hiking_menu.сorrection_coeff_pct/100)) AS carbohydrates,		
			
			SUM(
			IF(
				hiking_menu.is_optimize=1,
				((IFNULL(rp1.energy,IFNULL(rp2.energy, recipes_products.energy))/100)*IFNULL(recipes_structure_alt.amount, IFNULL((recipes_structure.amount/100)*recipes_products_alt.weight, recipes_structure.amount)) ),
				((recipes_products.energy/100)*recipes_structure.amount)
			)*(hiking_menu.сorrection_coeff_pct/100)
			) AS energy,		
			
			SUM(
			IF(
				hiking_menu.is_optimize=1,
				( IFNULL(recipes_structure_alt.amount,IFNULL((recipes_structure.amount/100)*recipes_products_alt.weight, recipes_structure.amount)) ),
				(recipes_structure.amount)
			)*(hiking_menu.сorrection_coeff_pct/100)) AS amount	

		FROM hiking_menu
			LEFT JOIN recipes ON recipes.id = hiking_menu.id_recipe 
			LEFT JOIN recipes_categories ON recipes.id_category = recipes_categories.id
			
			LEFT JOIN recipes_structure ON recipes_structure.id_recipe = recipes.id
			LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id
			
			LEFT JOIN recipes_structure_alt ON recipes_structure_alt.id_rs = recipes_structure.id 
			LEFT JOIN recipes_products AS rp1 ON recipes_structure_alt.id_product = rp1.id
			
			LEFT JOIN recipes_products_alt ON recipes_products_alt.id_product = recipes_products.id 
			LEFT JOIN recipes_products AS rp2 ON recipes_products_alt.id_alt = rp2.id
			
			
		WHERE hiking_menu.id_hiking={$id} GROUP BY  hiking_menu.id_recipe, hiking_menu.id_act, hiking_menu.date");
	if(!$q){die("Ошибка.".$mysqli->error);}
	
	//$recs_id = array();
	//$paylist_counts  = array();
	
	while($r = $q->fetch_assoc()){
		if($ext){
			$r['products'] = array();	
			$sq = $mysqli->query("

				SELECT 
					hiking_menu.is_optimize,
				users.name AS uname,
				users.surname AS usurname,
				forseuser.name AS fname,
				forseuser.surname AS fsurname,
				
				IFNULL(forseuser.name,users.name) AS aname,
				IFNULL(forseuser.surname,users.surname) AS asurname,
			
								
					
				IF(
					hiking_menu.is_optimize=1,
					SUM((IFNULL(rp1.protein,IFNULL(rp2.protein, recipes_products.protein))/100)*IFNULL(recipes_structure_alt.amount, IFNULL((recipes_structure.amount/100)*recipes_products_alt.weight, recipes_structure.amount))),
					SUM((recipes_products.protein/100)*recipes_structure.amount)
				)*(hiking_menu.сorrection_coeff_pct/100) AS protein,
				
				IF(
					hiking_menu.is_optimize=1,
					SUM( (IFNULL(rp1.fat,IFNULL(rp2.fat, recipes_products.fat))/100)*IFNULL(recipes_structure_alt.amount, IFNULL((recipes_structure.amount/100)*recipes_products_alt.weight, recipes_structure.amount))),
					SUM((recipes_products.fat/100)*recipes_structure.amount)
				)*(hiking_menu.сorrection_coeff_pct/100) AS fat,		
				
				IF(
					hiking_menu.is_optimize=1,
					SUM( (IFNULL(rp1.carbohydrates,IFNULL(rp2.carbohydrates, recipes_products.carbohydrates))/100)*IFNULL(recipes_structure_alt.amount, IFNULL((recipes_structure.amount/100)*recipes_products_alt.weight, recipes_structure.amount)) ),
					SUM((recipes_products.carbohydrates/100)*recipes_structure.amount)
				)*(hiking_menu.сorrection_coeff_pct/100) AS carbohydrates,		
				
				IF(
					hiking_menu.is_optimize=1,
					SUM((IFNULL(rp1.energy,IFNULL(rp2.energy, recipes_products.energy))/100)*IFNULL(recipes_structure_alt.amount, IFNULL((recipes_structure.amount/100)*recipes_products_alt.weight, recipes_structure.amount)) ),
					SUM((recipes_products.energy/100)*recipes_structure.amount)
				)*(hiking_menu.сorrection_coeff_pct/100) AS energy,	
					
					
					
					IF(
						hiking_menu.is_optimize=1,
						IFNULL(rp1.name,IFNULL(rp2.name, recipes_products.name)),
						recipes_products.name
					) AS name,
					IF(
						hiking_menu.is_optimize=1,
						SUM(IFNULL(recipes_structure_alt.amount*(hiking_menu.сorrection_coeff_pct/100),IFNULL(((recipes_structure.amount/100)*recipes_products_alt.weight)*(hiking_menu.сorrection_coeff_pct/100), recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100))) ),
						SUM(recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100))
					) AS amount,						
					IF(
						hiking_menu.is_optimize=1,
						IFNULL(rp1.id,IFNULL(rp2.id, recipes_products.id)),
						recipes_products.id
					) AS id_product,						
					IF(
						hiking_menu.is_optimize=1,
						(SELECT GROUP_CONCAT(CONCAT(weight,'|',value,'|', cost)) FROM recipes_products_units_values WHERE id_product=IFNULL(rp1.id,IFNULL(rp2.id, recipes_products.id)) GROUP BY recipes_products_units_values.id_product),
						(SELECT GROUP_CONCAT(CONCAT(weight,'|',value,'|', cost)) FROM recipes_products_units_values WHERE id_product=recipes_products.id GROUP BY recipes_products_units_values.id_product)
					) AS cost					

				FROM hiking_menu
					LEFT JOIN recipes ON recipes.id = hiking_menu.id_recipe 
					LEFT JOIN recipes_structure ON recipes_structure.id_recipe = recipes.id
					LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id
					LEFT JOIN recipes_structure_alt ON recipes_structure_alt.id_rs = recipes_structure.id 
					LEFT JOIN recipes_products AS rp1 ON (recipes_structure_alt.id_product = rp1.id)
					LEFT JOIN recipes_products_alt ON recipes_products_alt.id_product = recipes_products.id 
					LEFT JOIN recipes_products AS rp2 ON recipes_products_alt.id_alt = rp2.id
					LEFT JOIN users ON users.id = hiking_menu.assignee_user
					
					LEFT JOIN hiking_menu_products_force ON (hiking_menu.id_hiking = hiking_menu_products_force.id_hiking AND hiking_menu_products_force.id_product = IF(
						hiking_menu.is_optimize=1,
						IFNULL(rp1.id,IFNULL(rp2.id, recipes_products.id)),
						recipes_products.id
					))
					LEFT JOIN users AS forseuser ON forseuser.id = hiking_menu_products_force.id_user
				WHERE hiking_menu.id=".$r['id_hiking_menu']."   GROUP BY id_product");//	
			if(!$sq){die("Ошибка.".($mysqli->error));}	
			while($sr = $sq->fetch_assoc()){
				$r['products'][] = $sr;
			}	
				
		}
			
		$dishes[] = $r;
		
		
	}
	
	$paylist_counts = array();
	$recs_id = array();
	foreach($dishes as $dish){
		if(!in_array($dish['id'], $recs_id)){$recs_id[]=$dish['id'];}
		if(!isset($paylist_counts[$dish['id']])){ $paylist_counts[$dish['id']] = 0; }
		$paylist_counts[$dish['id']]++;
		for($i=0; $i<count($menus); $i++){
			if(!isset($menus[$i]['energy'])){$menus[$i]['energy']=0;}
			
			$menus[$i]['energy_target'] = intval($menus[$i]['act']['norm_kkal']);
			if(!isset($menus[$i]['dishes'])){$menus[$i]['dishes']=array();}
			
			if($dish['date']==date('Y-m-d',$menus[$i]['date']) &&  $dish['id_act']== $menus[$i]['act']['id'] ){
				$menus[$i]['energy']+= $dish['energy'];
				$menus[$i]['dishes'][] = $dish;
			}
		}
	}

	
	


$result['menu'] = $menus;


}

$paylist = array();

if(!isset($_GET['man'])){

if($optimize){
	$q = $mysqli->query("SELECT 
		GROUP_CONCAT(recipes_structure.id_recipe SEPARATOR ',') AS recs,
		GROUP_CONCAT(IFNULL(recipes_structure_alt.amount,recipes_structure.amount) SEPARATOR ',') AS recs_amo,
		recipes_structure.id_recipe,
		IFNULL(rp1.id, recipes_products.id) AS id_product, 
		IFNULL(rp1.name, recipes_products.name) AS name, 
		IFNULL(rp1.protein, recipes_products.protein)/100*IFNULL(recipes_structure_alt.amount,recipes_structure.amount) AS protein, 
		IFNULL(rp1.fat, recipes_products.fat)/100*IFNULL(recipes_structure_alt.amount,recipes_structure.amount) AS fat, 
		IFNULL(rp1.carbohydrates, recipes_products.carbohydrates)/100*IFNULL(recipes_structure_alt.amount,recipes_structure.amount) AS carbohydrates, 
		IFNULL(rp1.energy, recipes_products.energy)/100*IFNULL(recipes_structure_alt.amount,recipes_structure.amount) AS energy, 
		recipes_products.weight, 
		recipes_products.cost, 
		recipes_structure.id,
		(SELECT CONCAT(id_user,'|',id) FROM hiking_menu_products_force WHERE id_product=IFNULL(rp1.id, recipes_products.id) AND id_hiking={$id} LIMIT 1) AS force_user,
		SUM(IFNULL(recipes_structure_alt.amount,recipes_structure.amount)) AS amount,
		(SELECT GROUP_CONCAT(CONCAT(weight,'|',value,'|', cost)) FROM recipes_products_units_values WHERE id_product=IFNULL(rp1.id, recipes_products.id) GROUP BY recipes_products_units_values.id_product) AS cost
	FROM recipes_structure LEFT JOIN recipes_products ON recipes_products.id = recipes_structure.id_product 
		LEFT JOIN recipes_structure_alt ON recipes_structure_alt.id_rs = recipes_structure.id
		LEFT JOIN recipes_products AS rp1 ON rp1.id = recipes_structure_alt.id_product 
	WHERE recipes_structure.id_recipe IN(".implode(",", array_unique($recs_id)).")   AND recipes_structure.id_recipe NOT IN (SELECT id_recipe FROM hiking_menu_exclude_recipes  WHERE id_hiking ={$id}) GROUP BY recipes_structure.id_recipe,recipes_products.id
	ORDER BY amount DESC");
} else {

$q = $mysqli->query("SELECT 
		recipes_structure.id_recipe,
		recipes_products.id AS id_product, 
		recipes_products.name, 
		recipes_products.protein/100*recipes_structure.amount AS protein, 
		recipes_products.fat/100*recipes_structure.amount AS fat, 
		recipes_products.carbohydrates/100*recipes_structure.amount AS carbohydrates, 
		recipes_products.energy/100*recipes_structure.amount AS energy, 
		recipes_products.weight, 
		recipes_products.cost, 
		recipes_structure.id,
		(SELECT CONCAT(id_user,'|',id) FROM hiking_menu_products_force WHERE id_product=recipes_products.id AND id_hiking={$id} LIMIT 1) AS force_user,
		SUM(recipes_structure.amount) AS amount,weight,
		
		(SELECT GROUP_CONCAT(CONCAT(weight,'|',value,'|', cost)) FROM recipes_products_units_values WHERE id_product=recipes_products.id GROUP BY recipes_products_units_values.id_product) AS cost
		
	FROM recipes_structure LEFT JOIN recipes_products ON recipes_products.id = recipes_structure.id_product 
	WHERE recipes_structure.id_recipe IN(".implode(",", array_unique($recs_id)).")   AND recipes_structure.id_recipe NOT IN (SELECT id_recipe FROM hiking_menu_exclude_recipes  WHERE id_hiking ={$id}) GROUP BY recipes_products.id 
	ORDER BY amount DESC");
}
					if(!$q){die($mysqli->error);}
$summ = 0;



while($r=$q->fetch_assoc()){

	if($r['force_user'] && strlen($r['force_user'])){
		$tmp = explode('|', $r['force_user']);
		$r['force_user'] = $tmp[0];
		$r['force_user_id_row'] = $tmp[1];
	}




	$r['cou'] = $paylist_counts[$r['id_recipe']];
	$r['sum'] =( $r['amount'] * $paylist_counts[$r['id_recipe']]) * count($hiking['hikers']);
	$summ += ( $r['amount'] * $paylist_counts[$r['id_recipe']]);
	$addNew = true;
	
	
	for($i=0; $i<count($paylist); $i++){
		if($paylist[$i]['id_product'] === $r['id_product']){
			$paylist[$i]['sum'] += $r['sum'];
			$paylist[$i]['cou'] += $r['cou'];
			if(!(strlen($paylist[$i]['force_user'])>0) && strlen($r['force_user'])>0){$paylist[$i]['force_user'] = $r['force_user'];}
			$addNew = false;
			break;
		}
	}
	if($addNew){$paylist[] = $r;}
}

$result['paylist'] = $paylist;

$result['total']= $summ*count($hiking['hikers']);

$summ = ($equip_weight/count($hiking['hikers']))+$summ;

$result['norm'] = array("0"=>$summ*1.3, "1"=>$summ*1.2, "2"=>$summ*0.8);

$used_products = array();

//exit(json_encode($result));

//shuffle($paylist); //
$pcount = count($paylist);

$kcount=0;
$trycount = 0;
$j=0;
$koef = 1-0.01;
while($trycount<50 && count($paylist)>0){
	$j=0;
	$koef += 0.01;
while( $j<count($paylist) ){
	for($i=0; $i<count($hiking['hikers']); $i++){
		if(!isset($hiking['hikers'][$i]['norm'])){ $hiking['hikers'][$i]['norm'] = $result['norm'][ $hiking['hikers'][$i]['sex'] ];}
		if(!isset($hiking['hikers'][$i]['products'])){$hiking['hikers'][$i]['products']= array();}
		if(isset($paylist[$j]) &&  ($hiking['hikers'][$i]['weight']+($paylist[$j]['sum'])) < ($hiking['hikers'][$i]['norm']*$koef) ){
			if(!in_array($paylist[$j]['id_product'], $used_products)){
				
				if($paylist[$j]['force_user']>0 && $paylist[$j]['force_user']!=$hiking['hikers'][$i]['id_user']){
					continue;					
				}
				
				$used_products[] = $paylist[$j]['id_product'];
				$hiking['hikers'][$i]['weight']  += $paylist[$j]['sum'];
				$hiking['hikers'][$i]['products'][] = $paylist[$j];
				$kcount++;	
				
				

				
				for($x=0; $x<count($result['paylist']); $x++){
					if($result['paylist'][$x]['id_product']===$paylist[$j]['id_product']){
						$result['paylist'][$x]['user'] = $hiking['hikers'][$i];
						
					}
				}
				//$j++;
				
				break;
				
			}
		}

	}
	$j++;
};/*
for($i=0; $i<count($paylist); $i++){
	if(isset($paylist[$i]['user']) && isset($paylist[$i]['user']['id'])){
		unset($paylist);
	}
}*/
$trycount++;

}




} else { // if no MANual

$q = $mysqli->query("
					SELECT 
					 1 as cou,
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
						
						
						GROUP_CONCAT(CONCAT(IFNULL(forseuser.id, users.id),'|',IFNULL(forseuser.name, users.name),'|', IFNULL(forseuser.surname, users.surname), '|', IF(	
								hiking_menu.is_optimize=1,
								IFNULL(recipes_structure_alt.amount*(hiking_menu.сorrection_coeff_pct/100),IFNULL(((recipes_structure.amount/100)*recipes_products_alt.weight)*(hiking_menu.сorrection_coeff_pct/100), recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100))) ,
								recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100)
							),'|', hiking_menu.date)) AS users,
						
						
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
						LEFT JOIN users ON users.id = hiking_menu.assignee_user
						
						
						LEFT JOIN hiking_menu_products_force ON (hiking_menu.id_hiking = hiking_menu_products_force.id_hiking AND hiking_menu_products_force.id_product = IF(
							hiking_menu.is_optimize=1,
							IFNULL(rp1.id,IFNULL(rp2.id, recipes_products.id)),
							recipes_products.id
						))
						LEFT JOIN users AS forseuser ON forseuser.id = hiking_menu_products_force.id_user
						
						
						
					WHERE hiking_menu.id_hiking={$id} GROUP BY id_product");//
	if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
	$summ = 0;
	$userps = array();
	while($r = $q->fetch_assoc()){
		$r['sum'] = ( $r['amount'] ) * count($hiking['hikers']);
		$r['user'] = array();
		
		$tmp = explode(',', $r['users']);
		foreach($tmp as $y){
			$t = explode('|',$y);
			if(is_array($t) && count($t)>3){
				$r['user'][] = array(
					'id'		=> $t[0],
					'name'		=> $t[1],
					'surname'	=> $t[2],
					'amount'	=> $t[3]*count($hiking['hikers']),
					'date'		=> isset($t[4])?$t[4]:'-'/**/
				);
				
				for( $i=0; $i<count($hiking['hikers']); $i++){
					if($hiking['hikers'][$i]['id_user'] == $t[0]){
						$hiking['hikers'][$i]['weight']  += $t[3]*count($hiking['hikers']);
						
						if(!isset($userps[$i])){$userps[$i]=array();}
						if(!in_array($r['id_product'], $userps[$i])){
							$userps[$i][] = $r['id_product'];
							$hiking['hikers'][$i]['products'][] = $r;
						} 
						
							for($j=0; $j<count($hiking['hikers'][$i]['products']); $j++){
								if($hiking['hikers'][$i]['products'][$j]['id_product']==$r['id_product']){
									$hiking['hikers'][$i]['products'][$j]['sum'] = 0;
									foreach($r['user'] as $u){
										if($u['id']==$hiking['hikers'][$i]['id_user']){
											$hiking['hikers'][$i]['products'][$j]['sum'] += $u['amount'];
										}
									}
									
									
									
									//break;
								}
							}
						
						
					}
				}
	

				
				
			}
		
		}
		
		
		
		
		$paylist[] = $r;
		$summ += $r['amount'];
		
	
					
		
		
		
	}
	
	
	$result['paylist'] = $paylist;

	$result['total']= $summ*count($hiking['hikers']);

	$summ = ($equip_weight/count($hiking['hikers']))+$summ;

	$result['norm'] = array("0"=>$summ*1.3, "1"=>$summ*1.2, "2"=>$summ*0.8);
	

}






$result['hiking'] = $hiking;

if(isset($_POST['confirm'])){
	unset($result["can_confirm"]);
	if($mysqli->query("UPDATE hiking SET confirm_list_products='".$mysqli->real_escape_string(json_encode($result))."',
	confirm_list_date = NOW(),
	confirm_list_user={$id_user} WHERE id={$id} LIMIT 1")){
		die(json_encode(array("success"=>true)));
	} else {
	die(json_encode(array("error"=>$mysqli->error)));
	}
}

echo json_encode($result);

