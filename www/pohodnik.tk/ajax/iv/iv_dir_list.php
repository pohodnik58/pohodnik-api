<?
include("../../blocks/db.php"); //подключение к БД
$result = array();
$q = $mysqli->query("SELECT `id`, `name` FROM `iv_directories` WHERE 1");
while($r = $q->fetch_assoc()){
	$result[] = $r;
}
echo json_encode($result);
?>