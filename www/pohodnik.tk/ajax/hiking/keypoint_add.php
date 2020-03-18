<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_hiking = intval($_POST['id_hiking']);


$id_user = $_COOKIE["user"];


$q = $mysqli->query("SELECT id FROM  hiking WHERE  id_author={$id_user} AND id={$id_hiking} LIMIT 1");
if($q && $q->num_rows===1){

	 $v = "
			`id_hiking`={$id_hiking},
			`name`='".$mysqli->real_escape_string($_POST['name'])."',
			`desc`='".$mysqli->real_escape_string($_POST['desc'])."',
			`date_start`='".$mysqli->real_escape_string($_POST['d1'])."',
			`date_finish`='".$mysqli->real_escape_string($_POST['d2'])."',
			`lat`=".$mysqli->real_escape_string($_POST['lat']).",
			`lon`=".$mysqli->real_escape_string($_POST['lon'])."
		";
	$id = isset($_POST['id'])?intval($_POST['id']):0;
	if($id>0){
		$z = "UPDATE hiking_keypoints SET {$v} WHERE id={$id}";
	} else {
		$z = "INSERT INTO hiking_keypoints SET {$v}";
	}
	
	$q = $mysqli->query($z);
	if($q){
		$result['success'] = true;
		if($id===0){
			$result['id'] = $mysqli->insert_id;
		}
		die(json_encode($result));
	} else {
		die(json_encode(array("error"=>"Error ".$mysqli->error. " \r\n". $z)));
	}
	
} else {
	die(json_encode(array("error"=>"Access denied. ".$mysqli->error)));
}