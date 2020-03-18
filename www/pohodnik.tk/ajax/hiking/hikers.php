<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных

$id = intval($_GET['id']);
$id_user = $_COOKIE["user"];

$qi = $mysqli->query("SELECT  hiking_members.id_user, UNIX_TIMESTAMP(hiking_members.date) AS date , users.name, users.surname,  users.vk_id, users.photo_50, users.photo_100, users.sex
						FROM hiking_members LEFT JOIN users ON hiking_members.id_user = users.id
						WHERE hiking_members.id_hiking={$id} ORDER BY hiking_members.date");
$res = array();							
while($ri = $qi->fetch_assoc()){
	
	$ri["is_i_hiker"] = ($ri['id_user']==$id_user);
	$res[] = $ri;
}
exit(json_encode($res));