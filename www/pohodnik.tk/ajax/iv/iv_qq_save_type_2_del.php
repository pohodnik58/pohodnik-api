<?php // СПИСКИ
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_user = $_COOKIE["user"];
$id_qq = intval($_POST['id_qq']);
$id = intval($_POST['id']);
if($mysqli->query("DELETE FROM iv_qq_params_variants WHERE id={$id}")){
	$result['success'] = true;
} else {
	die(json_encode(array("error"=>"Ошибка удаления \r\n".$mysqli->error)));
}
echo json_encode($result);
?>