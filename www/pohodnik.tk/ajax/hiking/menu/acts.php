<?php //recipes_product_add.php

include("../../../blocks/db.php"); //подключение к БД
include("../../../blocks/for_auth.php"); //Только для авторизованных
include("../../../blocks/dates.php");
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
$result = array();
$id = intval($_GET['id_hiking']);
$auto = isset($_GET['auto']);
$res = array();
$q = $mysqli->query("SELECT hiking_schedule.name AS title, hiking_schedule.kkal AS kkal, UNIX_TIMESTAMP(hiking_schedule.d1)+{$time_offset} AS d1, UNIX_TIMESTAMP(hiking_schedule.d1)+{$time_offset} AS d2, food_acts.norm_kkal, food_acts.id, food_acts.name  FROM `hiking_schedule` LEFT JOIN food_acts ON food_acts.id=hiking_schedule.id_food_act WHERE hiking_schedule.id_hiking={$id} AND hiking_schedule.id_food_act IS NOT NULL ORDER BY hiking_schedule.d1");
if($q){
	$cur = ""; $buf=array();
	while($r = $q->fetch_assoc()){
		if($cur != date('Y-m-d',$r['d1']) ){
			if($cur != ""){ $res[] = $buf; }
			$buf = array("date"=> $r['d1'], "date_rus"=>date('d.m.Y H:i:s',$r['d1']), "day_rus"=>date('d.m.Y',$r['d1']), "day"=>date('Y-m-d',$r['d1']), "acts"=>array());
			$cur = date('Y-m-d',$r['d1']); 
		}
		$buf['acts'][] = array('id'=> $r['id'], 'norm_kkal'=>$r['norm_kkal'],'kkal'=>$r['kkal'], 'name'=>$r['name'], 'time'=>date('H:i', $r['d1']));	
		
	}
	$res[] = $buf; 
}
echo json_encode($res);