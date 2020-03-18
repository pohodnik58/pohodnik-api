<?php //recipes_product_add.php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id= intval($_POST['id']);
$amount = floatval($_POST['amount']);
$id_user = $_COOKIE["user"];


		
		if($mysqli->query("UPDATE recipes_structure SET amount={$amount} WHERE id={$id}")){
			die(json_encode(array("success"=>true)));
		} else {
			die(json_encode(array("error"=>"Ошибка. ".$mysqli->error)));
		}
		