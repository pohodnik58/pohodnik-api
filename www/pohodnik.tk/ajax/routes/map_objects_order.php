<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$ids = $_POST['ids'];
if(!is_array($ids)){exit(json_encode(array("error"=>"Ошибка входных данных")));}

for($i=0; $i<count($ids); $i++){
	$q = $mysqli->query("UPDATE route_objects SET `ord`='".($i+1)."' WHERE id=".$ids[$i]."");
}
exit(json_encode(array("success"=>true)));
?>