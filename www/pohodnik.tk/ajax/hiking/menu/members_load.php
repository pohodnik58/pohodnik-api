	<?php
	
	include("../../../blocks/db.php"); //подключение к БД
	$id_hiking = intval($_GET['id_hiking']);
	
	
	$where = " hiking_menu.id_hiking={$id_hiking} ";
	
	if(isset($_GET['id_user'])){
		if($_GET['id_user']>0){
			$where .= " AND  hiking_menu.assignee_user=".intval($_GET['id_user']); 
		} else {
			$where .= " AND  hiking_menu.assignee_user=".intval($_COOKIE["user"]); 
		}
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
$summ = 0;

$users = array();
$times = array();
while($r = $q->fetch_assoc()){
	$r['date_rus'] = date('d.m.y', $r['uts']);
	$r['time_rus'] = date('H:i', $r['uts']);
	$res[] = $r;
	$summ+=$r['amount'];
	if(!isset($users[$r['uid']])){
		$users[$r['uid']] = array(
			"id"=>$r['uid'],
			"i"=>$r['uid']==$_COOKIE["user"],
			"name"=>$r['uname'],
			"surname"=>$r['usurname'],
			"times"=>array(),
			"summ"=>0,
			"load"=>0
		);
	}
	
	if(!isset($users[$r['uid']]['times'][$r['uts']])){
		$users[$r['uid']]['times'][$r['uts']] = array(
			"summ"=>0,
			"list"=>array()
		);
	}
	
	$users[$r['uid']]['times'][$r['uts']]['list'][] = $r;
	$users[$r['uid']]['times'][$r['uts']]['summ']+=$r['amount'];
	$users[$r['uid']]['summ']+=$r['amount'];
	
	$times[$r['uts']] = array('rus'=>date('d.m.Y H:i',$r['uts']), 'users'=>array());
	/*foreach($users as $user){
		$times[$r['uts']]['users'][] = array($user['id'], 0);
	}*/
}


foreach($times as $t=>$v){
	foreach($users as $uid=>$u){
		if(!isset($times[$t]['users'][$uid])){
			$times[$t]['users'][$uid] = $users[$uid]['times'][$t]['summ'] ;
		}
	}
}

/*
echo '<html><head><meta charset="utf-8"><style>body {font-family:Roboto}</style></head><body>';
echo '<table border="1" cellspacing=0 cellpadding=3>';
$i=0;
$usersums=array();
foreach($times as $t=>$v){
	if($i==0){
		echo '<tr>';
			echo '<td>дата/участник</td>';
			foreach($v['users'] as $u=>$a){
			
				if(!isset($usersums[$u])){$usersums[$u]=$summ/count($users);}
			
				echo '<td>UserId:'.$u.'</td>';
			}
			
			foreach($v['users'] as $u=>$a){
			
			
			
				echo '<td> ost UserId:'.$u.'/'.round($usersums[$u]).'</td>';
			}
			
			foreach($v['users'] as $u=>$a){
			
			
			
				echo '<td> ost UserId:'.$u.'/'.round($usersums[$u]).'</td>';
			}
						
		echo '</tr>';	
	}
	
	echo '<tr>';
		echo '<td>'.$v['rus'].'</td>';
			foreach($v['users'] as $u=>$a){
				$usersums[$u] = $usersums[$u] - $a;
				echo '<td>'.($a).'</td>';
			}
			foreach($v['users'] as $u=>$a){
				
				echo '<td>'.round($usersums[$u]).'</td>';
			}	
			foreach($v['users'] as $u=>$a){
				
				echo '<td>'.round((($usersums[$u]/($summ/count($users)))*100)).'%</td>';
			}	
	echo '</tr>';
	$i++;
}


*/






echo (json_encode(array(
	"times"=>$times,
	"summ" => $summ,
	"count" => count($users),
	"total" => ($summ * count($users)),
	"users" => $users
)));//$users));


/*

echo (json_encode(array(
	"times"=>$times,
	"users"=>$users,
	"list"=>$res,
	"summ"=>$summ
)));*/