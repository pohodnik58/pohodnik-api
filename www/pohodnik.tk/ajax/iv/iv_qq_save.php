<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_user	= $_COOKIE["user"];
$id_iv		= intval($_POST['id_iv']);
$id_qq		= isset($_POST['id_qq'])?intval($_POST['id_qq']):0;
$name		= $mysqli->real_escape_string($_POST['name']);
$text 		= $mysqli->real_escape_string($_POST['text']);
$id_type 	= intval($_POST['id_type']);
$is_custom	= intval($_POST['is_custom']);
$is_require = intval($_POST['is_require']);
$is_private = intval($_POST['is_private']);
$is_multi	= intval($_POST['is_multi']);

if($id_qq>0){
	$q = $mysqli->query("UPDATE iv_qq SET 
							name = '{$name}',
							text = '{$text}',
							is_custom ={$is_custom},
							is_require = {$is_require},
							is_multi = {$is_multi},
							is_private = {$is_private}
						 WHERE id={$id_qq}
						");
	if(!$q){die(json_encode(array("error"=>"Ошибка обновления \r\n".$mysqli->error)));}
	$result['success'] = true;
} else {
	$q = $mysqli->query("INSERT INTO iv_qq SET 
							id_iv = {$id_iv},
							name = '{$name}',
							text = '{$text}', 
							id_type ={$id_type}, 
							is_custom ={$is_custom},
							is_require = {$is_require},
							is_multi = {$is_multi},
							is_private = {$is_private}
						");
	if(!$q){die(json_encode(array("error"=>"Ошибка добавления \r\n".$mysqli->error)));}
	$result['success'] = true;
	$result['new'] = true;
	$result['id'] = $mysqli->insert_id;
}
echo json_encode($result);
?>