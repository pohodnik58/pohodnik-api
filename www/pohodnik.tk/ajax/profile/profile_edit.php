<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$id_user = $_COOKIE["user"];
$new_name = $mysqli->real_escape_string(trim($_POST['new_name']));
$new_surname = $mysqli->real_escape_string(trim($_POST['new_surname']));
$new_dob = $mysqli->real_escape_string(trim($_POST['new_dob']));
$new_sex = $_POST['new_sex'];
$new_phone = $mysqli->real_escape_string(trim($_POST['new_phone']));
$new_email = $mysqli->real_escape_string(trim($_POST['new_email']));
$new_skype = $mysqli->real_escape_string(trim($_POST['new_skype']));
$new_icq = $mysqli->real_escape_string(trim($_POST['new_icq']));

$q = $mysqli->query("UPDATE users SET 	name='".$new_name."', 
										surname='".$new_surname."', 
										dob='".$new_dob."',
										sex=".$new_sex.",
										phone='".$new_phone."',
										email='".$new_email."', 
										skype='".$new_skype."', 
										icq='".$new_icq."'  
					WHERE id={$id_user}");
if(!$q){exit(json_encode(array("error"=>"Ошибка при редактировании пользовательских данных. \r\n")));}
echo(json_encode(array("msg"=>"Данные успешно обновлены. \r\n")));
?>