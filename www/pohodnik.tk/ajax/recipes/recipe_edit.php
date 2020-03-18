<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$id_user = $_COOKIE["user"];
$id=$_POST['id'];
$name=isset($_POST['name'])?$mysqli->real_escape_string(trim($_POST['name'])):NULL;
$promo=isset($_POST['promo'])?$mysqli->real_escape_string(trim($_POST['promo'])):NULL;
$text=isset($_POST['text'])?$mysqli->real_escape_string(trim($_POST['text'])):NULL;
$photo=isset($_POST['photo'])?$mysqli->real_escape_string(trim($_POST['photo'])):NULL;

if (isset($_COOKIE["user"]) && $_COOKIE["user"]>0){


if($photo){
	$q= $mysqli->query("SELECT photo FROM recipes WHERE id={$id} LIMIT 1");
	if($q && $q->num_rows===1){
		$r = $q->fetch_assoc();
		if(is_file("../../".$r['photo'])){
			unlink("../../".$r['photo']);
		}
	}
}

	$set = array();
	if($name){$set[] = "name='{$name}'";}
	if($promo){$set[] = "promo_text='{$promo}'";}
	if($text){$set[] = "text='{$text}'";}
	if($photo){$set[] = "photo='{$photo}'";}

		$res = $mysqli->query("UPDATE recipes SET ".implode(", ",$set)." WHERE id={$id}");
		if($res){
		echo json_encode(array("success"=>true));
		}else{
			exit(json_encode(array("error"=>"Ошибка при обновлении рецепта. \r\n", "err"=>$mysqli->error, "set"=>$set)));	
		};
	
}else{
	exit(json_encode(array("error"=>"Пользователь не авторизован. \r\n")));
};
?>