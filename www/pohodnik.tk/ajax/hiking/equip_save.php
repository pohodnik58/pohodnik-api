<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();

$id_hiking = intval($_POST['id_hiking']);
$id_equip = intval($_POST['id_equip']);
$id_user = $_COOKIE["user"];

$q = $mysqli->query("SELECT id FROM hiking_equipment WHERE `id_hiking`={$id_hiking} AND `id_user`={$id_user} AND `id_equip`={$id_equip} LIMIT 1");
if($q && $q->num_rows===1){
	exit(json_encode(array("error"=>"Уже добавлено")));
}
	if($mysqli->query("INSERT INTO `hiking_equipment` SET
						`id_hiking`={$id_hiking},
						`id_user`={$id_user},
						`id_equip`={$id_equip}
					  ")){
		$id = $mysqli->insert_id;
		exit(json_encode(array("success"=>"Добавлено: ".$name."", "id"=> $id)));
	}else{exit(json_encode(array("error"=>"Ошибка добавления. \r\n".$mysqli->error)));}

?>