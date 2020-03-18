<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$name = $mysqli->real_escape_string(trim($_POST['name']));
$desc = $mysqli->real_escape_string(trim($_POST['desc']));
$type = isset($_POST['type'])?intval($_POST['type']):1;
$d1 = ($mysqli->real_escape_string(trim($_POST['d1'])));
$d2 = ($mysqli->real_escape_string(trim($_POST['d2'])));
$id_user = $_COOKIE["user"];
$id = isset($_POST['id'])?intval($_POST['id']):0;
$id_route = intval($_POST['id_route']);
$id_region = intval($_POST['id_region']);
$color = ($mysqli->real_escape_string(trim($_POST['color'])));

if($d1>=$d2){exit(json_encode(array("error"=>"Дата начала должна быть раньше даты завершения")));}

if($id>0){
	if($mysqli->query("UPDATE `hiking` SET 
						`id_type`={$type},
						`name`='{$name}',
						`desc`='{$desc}',
						`start`='{$d1}',
						`finish`='{$d2}',
						`id_route`='{$id_route}',
						`color` = '{$color}'
					  WHERE id={$id}
					  ")){
		exit(json_encode(array("success"=>"Данные успешно сохранены", "id"=> $id)));
	}else{exit(json_encode(array("error"=>"Ошибка обновления . \r\n".$mysqli->error)));}
} else {

	if($mysqli->query("INSERT INTO `hiking` SET 
						`id_type`={$type},
						`name`='{$name}',
						`desc`='{$desc}',
						`start`='{$d1}',
						`finish`='{$d2}',
						`id_route`='{$id_route}',
						`color` = '{$color}',
						id_author = {$id_user},
						id_region = {$id_region}
					  ")){
		$id = $mysqli->insert_id;
		$q = $mysqli->query("INSERT INTO `hiking_members`
							(`id_hiking`, `id_user`, `date`,`is_admin`) VALUES ({$id},{$id_user}, NOW(),1)");
		exit(json_encode(array("success"=>"Поход создан", "id"=> $id)));
	}else{exit(json_encode(array("error"=>"Ошибка добавления. \r\n".$mysqli->error)));}
}
?>