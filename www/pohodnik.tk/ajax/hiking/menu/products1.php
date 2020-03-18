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
						MIN(hiking_menu.date) AS min_date,
						MAX(hiking_menu.date) AS max_date,
						COUNT(*) AS cou,
						
						UNIX_TIMESTAMP(MAX(hiking_menu.date)) - UNIX_TIMESTAMP(MIN(hiking_menu.date)) AS dif,
						
						hiking_menu.is_optimize
					FROM hiking_menu
						LEFT JOIN recipes ON recipes.id = hiking_menu.id_recipe 
						LEFT JOIN recipes_structure ON recipes_structure.id_recipe = recipes.id
						LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id
						LEFT JOIN recipes_structure_alt ON recipes_structure_alt.id_rs = recipes_structure.id 
						LEFT JOIN recipes_products AS rp1 ON (recipes_structure_alt.id_product = rp1.id)
						LEFT JOIN recipes_products_alt ON recipes_products_alt.id_product = recipes_products.id 
						LEFT JOIN recipes_products AS rp2 ON recipes_products_alt.id_alt = rp2.id
					WHERE hiking_menu.id_hiking={$id_hiking} ".$addwhere." GROUP BY id_product ORDER BY cou DESC,  amount DESC,dif DESC,  min_date DESC");//
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
while($r = $q->fetch_assoc()){
	$res[] = $r;
}
/*						
	GROUP_CONCAT(CONCAT(IF(hiking_menu.is_optimize=1,IF(LENGTH(recipes.name_opt)>0,recipes.name_opt,recipes.name ), recipes.name ),'|',hiking_menu.date,'|',IF(	
		hiking_menu.is_optimize=1,
		IFNULL(recipes_structure_alt.amount*(hiking_menu.сorrection_coeff_pct/100),IFNULL(((recipes_structure.amount/100)*recipes_products_alt.weight)*(hiking_menu.сorrection_coeff_pct/100), recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100))) ,
		recipes_structure.amount*(hiking_menu.сorrection_coeff_pct/100)
	))) AS use9,

*/

	$users = array(
		array("id"=>3, "name"=>"FN", "amount"=>0, "products"=>array(), 'coef'=>1.1),
		array("id"=>34, "name"=>"BE", "amount"=>0, "products"=>array(), 'coef'=>0.9),
		array("id"=>47, "name"=>"ZK", "amount"=>0, "products"=>array(), 'coef'=>1.1)
	);
	$summ = 0;
	echo '<html><head><meta charset="utf-8"><style>body {font-family:Roboto}</style></head><body><table border="1" cellspacing=0 cellpadding=3>';
	echo '<tr><th>№пп</th>';
		foreach($res[0] as $k=>$v){
			echo '<th>'.$k.'</th>';
		}
	echo '</tr>';
	
	$i=1; $j=0;
	foreach($res as $r){
		
			echo '<tr>';
				echo '<td>'.$i.'</td>';
				foreach($r as $k=>$v){
					echo '<td>'.$v.'</td>';
				}
			echo '</tr>'; 
		$i++;
		$summ+= $r['amount'];

	}
	
	$iter = 1;
	$koef=1;$i=1; $j=0; $used=array(); $used_names=array();$ostatok=array();
	$try = true;

	/*	foreach($res as $r){
			$next = (($users[$j]['amount'] + $r['amount'])*count($users));
			if($next < $summ && !in_array($r['id_product'], $used)){
				$users[$j]['lim'] = $summ;
				$users[$j]['amount'] += $r['amount'];
				$users[$j]['products'][] = $r['id_product'];
				$used[] = $r['id_product'];
				$used_names[] = $r['name'];
							
			} else {
				$ostatok[] = $r;
			}
			if($j>=COUNT($users)-1){$j=0;} else {$j++;}	
			
		}	*/
		$ostatok = $res;
		while($koef<1.2 && count($res)>count($used)){
			$arr = array_reverse($ostatok);
			$ostatok = array();
			foreach($arr as $r){
				$next = (($users[$j]['amount'] + $r['amount'])*count($users))*(2-$users[$j]['coef']);
				if($next < $summ*$koef && !in_array($r['id_product'], $used)){
					$users[$j]['lim'] = $summ;
					$users[$j]['amount'] += $r['amount'];
					$users[$j]['products'][] = $r['id_product'];
					$used[] = $r['id_product'];
					$used_names[] = $r['name'];
								
				} else {
					$ostatok[] = $r;
				}
				if($j>=COUNT($users)-1){$j=0;} else {$j++;}	
				
			}	
			$koef+=0.05;
		}
	
	echo '</table><pre>';
	
	echo count($res)."/".count($used).' max coeff='.$koef."\r\n";
	
print_r($users);

echo "\r\n".'остаток после первой итерации'."\r\n";

print_r($ostatok);


echo '</pre><h2>'.($summ).'*'.count($users).'='.$summ*count($users).'</h2><pre>';

	
	
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
	users.name AS uname,
	users.surname AS usurname,
	users.id AS uid,
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
	) AS energy,
	hiking_menu.date
	
	
	
	
	
	
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
WHERE {$where} ORDER BY hiking_schedule.d1, recipes.id");

if(!$q){die(json_encode(array("error"=>$mysqli->error)));}

$res = array();
$dates = array();
$log = array();

$tmp_user_summ = array();
foreach($users as $user){
$tmp_user_summ[$user['id']] = $user['amount']*count($users) ;
}



while($r = $q->fetch_assoc()){
	$r['date_rus'] = date('d.m.y', $r['uts']);
	$r['time_rus'] = date('H:i', $r['uts']);
	$res[] = $r;
	if(!isset($dates[$r['date']])){$dates[$r['date']]=array();}




	foreach($users as $user){
	
		
	
		if(!isset($dates[$r['date']][$user['id']])){$dates[$r['date']][$user['id']] = $tmp_user_summ[$user['id']];}
		if(in_array($r['id_product'],$user['products'])){
			$tmp_user_summ[$user['id']] = $tmp_user_summ[$user['id']]-( $r['amount']*count($users) );
			$dates[$r['date']][$user['id']]=$tmp_user_summ[$user['id']];
				
			
		}
	}
	
}







print_r($dates);



	echo '<table border="1" cellspacing=0 cellpadding=3>';
	echo '<tr><th>№пп</th>';
		foreach($dates as $k->$x){
				foreach($x as $u->$v){
					echo '<td>'.$u.'</td>';
				}
		}
	echo '</tr>';
	
	$i=1; $j=0;
	foreach($dates as $r){
		
			echo '<tr>';
				echo '<td>'.$i.'</td>';
				foreach($r as $k=>$v){
					echo '<td>'.$v.'</td>';
				}
			echo '</tr>'; 
		$i++;

	}
	echo '</table><pre>';


	echo '<table border="1" cellspacing=0 cellpadding=3>';
	echo '<tr><th>№пп</th>';
		foreach($res[0] as $k=>$v){
			echo '<th>'.$k.'</th>';
		}
	echo '</tr>';
	
	$i=1; $j=0;
	foreach($res as $r){
		
			echo '<tr>';
				echo '<td>'.$i.'</td>';
				foreach($r as $k=>$v){
					echo '<td>'.$v.'</td>';
				}
			echo '</tr>'; 
		$i++;

	}
	echo '</table><pre>';
