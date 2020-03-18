<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$id_user = $_COOKIE["user"];

$q = $mysqli->query("
						SELECT 
							DISTINCT
							users.id,
							users.name,
							users.surname
						FROM `user_food_pref`
							LEFT JOIN users ON users.id = user_food_pref.id_user
");



if($q){
	$result = array();
	while($r = $q->fetch_assoc()){
		$result[] = $r;
	}
	echo json_encode($result);
}else{exit(json_encode(array("error"=>"Не могу получить список походов \r\n".$mysqli->error)));}

?>