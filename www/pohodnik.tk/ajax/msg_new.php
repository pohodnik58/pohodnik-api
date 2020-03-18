<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$current_user = $_COOKIE["user"];
$id_recipient = $_POST['id_recipient'];
$q = $mysqli->query();
if(!$q){exit(json_encode(array("error"=>"Ошибка при отправке сообщения. \r\n")));}
echo(json_encode($q->fetch_assoc()));
?>