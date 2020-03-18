<?php
include("../../../blocks/db.php"); //подключение к БД
include("../../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_hiking = isset($_POST['id_hiking'])?intval($_POST['id_hiking']):'NULL';
$id_user = isset($_COOKIE["user"])?$_COOKIE["user"]:0;

if(!($id_hiking>0)){die(json_encode(array("error"=>"id_hiking is undefined")));}
$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_user} LIMIT 1");
if($q && $q->num_rows===0){
	$q = $mysqli->query("SELECT id FROM hiking_editors WHERE id_hiking={$id_hiking}  AND is_guide=1  AND id_user = {$id_user} LIMIT 1");
	if($q && $q->num_rows===0){
		die(json_encode(array("error"=>"Нет доступа")));
	}
}



$url = $mysqli->real_escape_string($_POST['url']);
$name = $mysqli->real_escape_string($_POST['name']);
$date_start = intval($_POST['date_start']);
$date_finish = intval($_POST['date_finish']);
$distance = intval($_POST['distance']);
$speed_moving = intval($_POST['speed_moving']);
$speed_total = intval($_POST['speed_total']);
$moving_pace = intval($_POST['moving_pace']);
$alt_min = intval($_POST['alt_min']);
$alt_max = intval($_POST['alt_max']);
$alt_up_sum = intval($_POST['alt_up_sum']);
$alt_down_sum = intval($_POST['alt_down_sum']);
$time_in_moution = intval($_POST['time_in_moution']);


/*

id_hiking
url
name
date_start
date_finish
distance
speed_min
speed_max
speed_avg
alt_min
alt_max
alt_up_sum
alt_down_sum
time_in_moution

 */


$q = $mysqli->query("INSERT INTO `hiking_tracks` SET 
	`id_user`={$id_user},
	`id_hiking`={$id_hiking},
	`url`='{$url}',
	`name`='{$name}',
	`date_create`=NOW(),
	`date_start`='".date('Y-m-d H:i:s', $date_start)."',
	`date_finish`='".date('Y-m-d H:i:s', $date_finish)."',
	`distance`={$distance},
	`speed_moving`={$speed_moving},
	`speed_total`={$speed_total},
	`moving_pace`={$moving_pace},
	`alt_min`={$alt_min},
	`alt_max`={$alt_max},
	`alt_up_sum`={$alt_up_sum},
	`alt_down_sum`={$alt_down_sum},
	`time_in_moution`={$time_in_moution}
");

if(!$q){die(array('error'=>$mysqli->error));}
die(array('success'=>true,'id'=>$mysqli->insert_id));
?>