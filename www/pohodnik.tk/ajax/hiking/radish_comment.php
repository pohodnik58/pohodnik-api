<?
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных

$res = array();
$id_user = $_COOKIE["user"];
$id = intval($_POST['id']);
$com = $mysqli->real_escape_string(trim($_POST['comment']));
if(!strlen($com)>0){die(json_encode(array('error'=>"Коментарий не должен быть пустым")));}
$q = $mysqli->query("UPDATE hiking_radish SET comment='{$com}' WHERE id={$id}");
 
if($q){
	die(json_encode(array("success"=>true)));
} else {
	die(json_encode(array("error"=>$mysqli->error)));
}

