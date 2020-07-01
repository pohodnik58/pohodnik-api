<?php //recipes_product_add.php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
include("../../blocks/dates.php"); //Только для авторизованных
$result = array();
$id_user = $_COOKIE["user"];
$id_hiking = intval($_GET['id_hiking']);
if(!($id_hiking>0)){die(json_encode(array("error"=>"Undefined id_hiking")));}
$claus = "";
if(isset($_GET['day'])){
	$claus .= " AND hiking_schedule.d1 BETWEEN '".$_GET['day']." 00:00:00' AND '".$_GET['day']." 23:59:59' ";
}


$q = $mysqli->query("SELECT 
		hiking_schedule.id, 
		hiking_schedule.id_hiking, 
		UNIX_TIMESTAMP(hiking_schedule.d1)+{$time_offset} AS uts1,  
		UNIX_TIMESTAMP(hiking_schedule.d2)+{$time_offset} AS uts2, 
		hiking_schedule.name, 
		hiking_schedule.id_food_act, 
		hiking_schedule.id_route_object,
		hiking_schedule.kkal,
		route_objects.name AS name_routeobject,
		route_objects.distance AS routeobject_distance,
		route_objects.`desc` AS routeobject_description,
		food_acts.name AS name_food_act,
		food_acts.norm_kkal AS food_act_norm_kkal
	FROM `hiking_schedule` 
		LEFT JOIN food_acts ON food_acts.id=hiking_schedule.id_food_act
		LEFT JOIN route_objects ON route_objects.id=hiking_schedule.id_route_object
	WHERE hiking_schedule.id_hiking={$id_hiking} {$claus} ORDER BY hiking_schedule.d1");
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
while($r = $q->fetch_assoc()){

	$r['d1'] = array(
		'day'=>date('d.m.Y', $r['uts1']),
		'time'=>date('H:i', $r['uts1']),
		'sql'=>date('Y-m-d H:i:s',$r['uts1'])
	); 
	$r['d2'] = array(
		'day'=>date('d.m.Y', $r['uts2']),
		'time'=>date('H:i', $r['uts2']),
		'sql'=>date('Y-m-d H:i:s',$r['uts2'])
	);
	$result[] = $r;
}
echo json_encode($result);