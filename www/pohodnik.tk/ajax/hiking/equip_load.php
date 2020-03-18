<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_hiking = intval($_GET['id_hiking']);
$id_user = $_COOKIE["user"];
$q = $mysqli->query("SELECT id FROM hiking_equipment WHERE id_user={$id_user} AND id_hiking={$id_hiking}");
if($q && $q->num_rows>0){

	$q = $mysqli->query("SELECT id FROM hiking_equipment WHERE id_user={$id_user} AND id_hiking={$id_hiking} AND id_equip>0");
	if($q && $q->num_rows>0){ // по новому
	
		$q = $mysqli->query("SELECT  he.id, he.`id_user`, user_equip.`name`, user_equip.`weight`/1000 AS weight, user_equip.`value` , he.is_confirm,
							users.name AS uname, users.surname AS usurname, users.photo_50
							FROM hiking_equipment  AS he
							LEFT JOIN users ON (users.id = he.id_user)
							LEFT JOIN user_equip ON (user_equip.id=he.id_equip)
						 WHERE he.id_hiking = {$id_hiking} ORDER BY users.id"); 
		if($q){
			while($r = $q->fetch_assoc()){
				$r['my'] = $r['id_user'] == $id_user;
				$result[] = $r;
			}
			exit(json_encode($result));
		}else{exit(json_encode(array("error"=>"Ошибка . \r\n".$mysqli->error)));}
	
	} else { // По старому

		$q = $mysqli->query("SELECT  he.id, he.`id_user`, he.`name`, he.`weight`, he.`value` , he.is_confirm,
							users.name AS uname, users.surname AS usurname, users.photo_50
							FROM hiking_equipment  AS he
							LEFT JOIN users ON (users.id = he.id_user)
						 WHERE he.id_hiking = {$id_hiking} ORDER BY users.id"); 
		if($q){
			while($r = $q->fetch_assoc()){
				$r['my'] = $r['id_user'] == $id_user;
				$result[] = $r;
			}
			exit(json_encode($result));
		}else{exit(json_encode(array("error"=>"Ошибка . \r\n".$mysqli->error)));}
	
	}
}
	exit(json_encode($result));
?>