<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$id_user = $_COOKIE["user"];
$id_category=$_POST['id_category'];
$name=$mysqli->real_escape_string(trim($_POST['name_recipe']));
$promo_text=$mysqli->real_escape_string(trim($_POST['promo_text']));
$text=$mysqli->real_escape_string(trim($_POST['text']));
$photo=isset($_POST['photo'])?$mysqli->real_escape_string(trim($_POST['photo'])):'';


if (isset($_COOKIE["user"]) && $_COOKIE["user"]>0){
	$q = $mysqli->query("SELECT * from recipes WHERE name = '{$name}' LIMIT 1");
	if($q && $q->num_rows===1){
		exit(json_encode(array("error"=>"Такое блюдо уже есть в списке!. \r\n".$mysqli->error)));
	}else{
		$res = $mysqli->query("INSERT INTO recipes SET
					`id_category`='".$id_category."',
					`id_author`='".$id_user."',
					`name`='{$name}',
					`promo_text`='{$promo_text}',
					`text`='{$text}',
					`photo`='{$photo}'");
		if($res){
		echo json_encode(array("msg"=>"Рецепт ".$name." добавлен список рецептов!. \r\n"));
		}else{
			exit(json_encode(array("error"=>"Ошибка при добавлении рецепта. \r\n")));	
		};
	}
}else{
	exit(json_encode(array("error"=>"Пользователь не авторизован. \r\n")));
};
?>