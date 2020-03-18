<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$id_user = $_COOKIE["user"];
$q = $mysqli->query("SELECT id, name, surname, sex, dob, reg_date, ava, photo_200_orig, photo_50, email, phone, skype, icq, skills FROM users WHERE id={$id_user} LIMIT 1;");
if(!$q){exit(json_encode(array("error"=>"Ошибка при запросе пользовательских данных. \r\n")));}
echo(json_encode($q->fetch_assoc()));

?>