<?php //recipes_product_add.php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_recipe = intval($_POST['id_recipe']);
$id_product = intval($_POST['id_product']);
$id_user = $_COOKIE["user"];
$q = $mysqli->query("SELECT id FROM recipes_structure WHERE id_recipe={$id_recipe} AND id_product={$id_product} LIMIT 1");
if($q && $q->num_rows === 1){ die(json_encode(array("error"=>"Уже добавлен.".$mysqli->error))); }

		
		if($mysqli->query("INSERT INTO recipes_structure SET id_recipe={$id_recipe}, id_product={$id_product}")){
			die(json_encode(array("success"=>true, "id"=>$mysqli->insert_id)));
		} else {
			die(json_encode(array("error"=>"Ошибка. ".$mysqli->error)));
		}
		

