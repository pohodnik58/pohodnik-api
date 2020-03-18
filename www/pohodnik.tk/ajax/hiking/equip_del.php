<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$id = isset($_POST['id'])?intval($_POST['id']):0;
if($id>0){
	if($mysqli->query("DELETE FROM `hiking_equipment` WHERE id={$id}  ")){
		exit(json_encode(array("success"=>"Запись удалена", "id"=> $id)));
	}else{exit(json_encode(array("error"=>"Ошибка удаления . \r\n".$mysqli->error)));}
}
?>