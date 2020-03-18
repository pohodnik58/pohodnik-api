<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id = intval($_POST['id']);
$q = $mysqli->query("SELECT `name` FROM `routes` WHERE name='".trim($mysqli->real_escape_string($_POST['name']))."'");
if($q && $q->num_rows>0 && $id ===0){exit(json_encode(array("error"=>"Уже есть маршрут с таким наименованием. Задайте другое имя.")));}
else if(($q && $q->num_rows===0) || $id > 0){

$id_user = $_COOKIE["user"];
if($id ===0){
$coords = "53.132729914996006,45.024418952452734";



$q = $mysqli->query("INSERT INTO `routes` SET 
						`name`='".trim($mysqli->real_escape_string($_POST['name']))."',
						`desc`='".trim($mysqli->real_escape_string($_POST['desc']))."',
						`center_coordinates`='".$coords."',
						`zoom` = 12,
						`length`= 0,
						`id_author`=".$id_user."
						
					");
	if($q){
		exit(json_encode(
							array(
									"success"=>"Маршрут успешно добавлен",
									"id"=>$mysqli->insert_id
								)
						));
								
	}else{exit(json_encode(array("error"=>"Ошибка добавления. \r\n".$mysqli->error)));}

} else if($id>0){

$q = $mysqli->query("UPDATE `routes` SET 
						`name`='".trim($mysqli->real_escape_string($_POST['name']))."',
						`desc`='".trim($mysqli->real_escape_string($_POST['desc']))."',
						`center_coordinates`='".trim($mysqli->real_escape_string($_POST['center']))."',
						`zoom`=".intval($_POST['zoom']).",
						`length`=0,
						`id_author`=".$id_user.",
						`id_type`=".intval($_POST['type'])."
						WHERE id={$id}
					");
					
					
	if($q){
		exit(json_encode(
							array(
									"success"=>"Данные успешно сохранены",
									"id"=>$id
								)
						));
								
	}else{exit(json_encode(array("error"=>"Ошибка обновления данных. \r\n".$mysqli->error)));}
}

}else{exit(json_encode(array("error"=>"Ошибка при получении данных о существующих маршрутах. \r\n".$mysqli->error)));}

?>