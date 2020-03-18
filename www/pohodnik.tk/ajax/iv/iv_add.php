<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$name = $mysqli->real_escape_string(trim($_POST['name']));
$desc = $mysqli->real_escape_string(trim($_POST['desc']));
$hello_text = $mysqli->real_escape_string(trim($_POST['hello_text']));
$by_text = $mysqli->real_escape_string(trim($_POST['by_text']));
$member_limit = intval($_POST['member_limit']);
$d1 = strtotime($mysqli->real_escape_string(trim($_POST['d1']." 00:00:00")));
$d2 = strtotime($mysqli->real_escape_string(trim($_POST['d2']." 23:59:59")));
$id_user = $_COOKIE["user"];
$id = isset($_POST['id'])?intval($_POST['id']):0;
$id_hiking = isset($_POST['id_hiking'])?intval($_POST['id_hiking']):0;
$main = isset($_POST['main'])?intval($_POST['main']):0;
if($d1>=$d2){exit(json_encode(array("error"=>"Дата начала должна быть раньше даты завершения")));}

if($id>0){
	if($mysqli->query("UPDATE `iv` SET 
						`name`='{$name}',
						`desc`='{$desc}',
						`id_author`={$id_user},
						`date_start`='".date('Y-m-d H:i:s',$d1)."',
						`date_finish`='".date('Y-m-d H:i:s',$d2)."',
						`hello_text`='{$hello_text}',
						`by_text`='{$by_text}',
						`members_limit`={$member_limit},
						`id_hiking`	=	{$id_hiking},
						`main`		=	{$main}
					  WHERE id={$id}
					  ")){
		exit(json_encode(array("success"=>"Данные успешно сохранены", "id"=> $id)));
	}else{exit(json_encode(array("error"=>"Ошибка обновления опроса. \r\n".$mysqli->error)));}
} else {

	if($mysqli->query("INSERT INTO `iv` SET 
						`name`='{$name}',
						`desc`='{$desc}',
						`id_author`={$id_user},
						`date_start`='".date('Y-m-d H:i:s',$d1)."',
						`date_finish`='".date('Y-m-d H:i:s',$d2)."',
						`hello_text`='{$hello_text}',
						`by_text`='{$by_text}',
						`members_limit`={$member_limit},
						`id_hiking`	=	{$id_hiking},
						`main`		=	{$main}
					  ")){
		exit(json_encode(array("success"=>"Опрос создан", "id"=> $mysqli->insert_id)));
	}else{exit(json_encode(array("error"=>"Ошибка добавления опроса. \r\n".$mysqli->error)));}
}




?>