<?php
include("../../blocks/db.php"); //подключение к БД
$result=array();$id_user = $_COOKIE["user"];
$add_wh = "";
if(isset($_GET['is_can_pref'])){
	$add_wh = " AND is_can_pref=1";
}


$q = $mysqli->query("SELECT `id`, norm_kkal,`name`, `time`, `coeff_pct`, `is_can_pref` FROM `food_acts` WHERE 1 {$add_wh}");
if(!$q){exit(json_encode(array("error"=>"Ошибка при запросе. \r\n")));}


while($r = $q->fetch_assoc()){
	$result[] = $r;		
}

exit(json_encode($result));
