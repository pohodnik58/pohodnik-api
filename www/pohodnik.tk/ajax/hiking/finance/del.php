<?php

include("../../../blocks/db.php"); //подключение к БД
include("../../../blocks/for_auth.php"); //Только для авторизованных
$id_user = $_COOKIE["user"];


$id = intval($_POST['id']); 


$q = $mysqli->query("DELETE FROM `hiking_finance` WHERE id={$id} AND id_author={$id_user}");
if(!$q){exit(json_encode(array("error"=>"Ошибка при . \r\n")));}
echo(json_encode(array("success"=>true, "msg"=>"Данные успешно. \r\n".$mysqli->error)));
?>