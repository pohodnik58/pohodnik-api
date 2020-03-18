<?
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$name = $mysqli->real_escape_string(trim($_POST['name']));
$weight = floatval($_POST['weight']);
$value = floatval($_POST['value']);
$id = isset($_POST['id'])?intval($_POST['id']):0;
$photo = isset($_POST['photo'])?$mysqli->real_escape_string(trim($_POST['photo'])):'';

$id_user = $_COOKIE["user"];
$z = ($id>0?"UPDATE":"INSERT INTO")." `user_backpacks` SET 
 `id_user`={$id_user},
 `name`='{$name}',
 `weight`={$weight},
 `value`={$value},
 `photo`='{$photo}'
 ".($id>0?" WHERE id={$id} AND id_user={$id_user}":"");



$q = $mysqli->query($z);
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
if(!$id>0){$id=$mysqli->insert_id;}
die(json_encode(array("success"=>true, "id"=>$id)));