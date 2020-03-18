<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id = intval($_POST['id']);
$name = $mysqli->real_escape_string(trim(($_POST['name'])));
$value = $mysqli->real_escape_string(trim($_POST['value']));
$id_user = isset($_COOKIE["user"])?$_COOKIE["user"]:0;
if($id>0 && $id_user>0){

	$q = $mysqli->query("UPDATE route_objects 
						SET `{$name}`='{$value}', id_editor={$id_user}, date_last_modif='".date('d.m.Y H:i:s')."' 
						WHERE id={$id}
						");
	if($q){
		exit(json_encode(array("success"=>$id)));
	} else {exit(json_encode(array("error"=>"Ошибка обновления данных".$mysqli->error)));}
}else{exit(json_encode(array("error"=>"Не определен объект")));}
?>