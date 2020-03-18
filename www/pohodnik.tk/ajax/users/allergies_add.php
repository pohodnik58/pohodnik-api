<?
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$name = $mysqli->real_escape_string(trim($_POST['name']));
$comment = $mysqli->real_escape_string(trim($_POST['comment']));
$id_product = isset($_POST['id_product'])?intval($_POST['id_product']):'NULL';
$id = isset($_POST['id'])?intval($_POST['id']):0;
$type = isset($_POST['type'])?intval($_POST['type']):3;
//
$id_user = $_COOKIE["user"];
$z = ($id>0?"UPDATE":"INSERT INTO")." `user_allergies` SET 
 `id_user`={$id_user},
 `name`='{$name}',
 `comment`='{$comment}',
 `id_product`={$id_product},
 `type`={$type}
 ".($id>0?" WHERE id={$id}":"");

$q = $mysqli->query($z);
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
if(!$id>0){$id=$mysqli->insert_id;}
die(json_encode(array("success"=>true, "id"=>$id)));