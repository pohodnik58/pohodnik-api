<?
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных

$id = isset($_POST['id'])?intval($_POST['id']):0;
$id_user = $_COOKIE["user"];
$z = " DELETE FROM `user_allergies`  WHERE id={$id} AND id_user={$id_user}";

$q = $mysqli->query($z);
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
die(json_encode(array("success"=>true, "id"=>$id)));