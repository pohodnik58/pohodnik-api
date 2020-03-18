<?php // СПРАВОШНИКИ)))
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_user = $_COOKIE["user"];
$id_qq			= intval($_POST['id_qq']);
$id				= isset($_POST['id'])?intval($_POST['id']):0;
$id_dir			= intval($_POST['id_dir']);

if($id>0){
	$q = $mysqli->query("UPDATE iv_qq_params_dir SET `id_dir`={$id_dir} WHERE id_qq={$id_qq} AND id={$id} ");
	if(!$q){die(json_encode(array("error"=>"Ошибка обновления \r\n".$mysqli->error)));}
	$result['success'] = true;
} else {
	$q = $mysqli->query("INSERT INTO iv_qq_params_dir SET `id_dir` ={$id_dir}, `id_qq` = {$id_qq}");
	if(!$q){die(json_encode(array("error"=>"Ошибка добавления \r\n".$mysqli->error)));}
	$result['success'] = true;
	$result['new'] = true;
	$result['id'] = $mysqli->insert_id;
}
echo json_encode($result);

?>
