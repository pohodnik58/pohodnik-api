<?php // ВВОД
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_user		= $_COOKIE["user"];

$id_qq			= intval($_POST['id_qq']);
$id				= isset($_POST['id'])?intval($_POST['id']):0;
$type			= $mysqli->real_escape_string($_POST['type']);
$pattern		= $mysqli->real_escape_string($_POST['pattern']);
$placeholder	= $mysqli->real_escape_string($_POST['placeholder']);
$min			= $mysqli->real_escape_string($_POST['min']);
$max			= $mysqli->real_escape_string($_POST['max']);
$step			= floatval($_POST['step']);

if(!$id_qq){die(json_encode(array("error"=>"нЕКУДА")));}
if($id>0){
	$q = $mysqli->query("UPDATE iv_qq_params_input SET 
							`type`			='{$type}',
							`pattern`		='{$pattern}',
							`placeholder`	='{$placeholder}',
							`min`			='{$min}',
							`max`			='{$max}',
							`step`			={$step}
						 WHERE id={$id}
						");
	if(!$q){die(json_encode(array("error"=>"Ошибка обновления \r\n".$mysqli->error)));}
	$result['success'] = true;
} else {
	$q = $mysqli->query("INSERT INTO iv_qq_params_input SET 
							`type`			='{$type}',
							`id_qq`			= {$id_qq},
							`pattern`		='{$pattern}',
							`placeholder`	='{$placeholder}',
							`min`			='{$min}',
							`max`			='{$max}',
							`step`			={$step}
						");
	if(!$q){die(json_encode(array("error"=>"Ошибка добавления \r\n".$mysqli->error)));}
	$result['success'] = true;
	$result['new'] = true;
	$result['id'] = $mysqli->insert_id;
}
echo json_encode($result);
?>