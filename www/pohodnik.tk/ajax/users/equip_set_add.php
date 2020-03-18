<?
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$name = $mysqli->real_escape_string(trim($_POST['name']));
$desc = $mysqli->real_escape_string(trim($_POST['desc']));
$id = isset($_POST['id'])?intval($_POST['id']):0;
$id_backpack = isset($_POST['id_backpack']) && intval($_POST['id_backpack'])>0?intval($_POST['id_backpack']):'NULL';
$id_hiking = isset($_POST['id_hiking']) && intval($_POST['id_hiking'])>0?intval($_POST['id_hiking']):'NULL';
$id_user = $_COOKIE["user"];
$z = ($id>0?"UPDATE":"INSERT INTO")." `user_equip_sets` SET 
 `id_user`={$id_user},
 `name`='{$name}',
 `description`='{$desc}',
 `date_update`=NOW(),
 `id_backpack`={$id_backpack},
 `id_hiking`={$id_hiking}
 ".($id>0?" WHERE id={$id}":"");

$q = $mysqli->query($z);
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
if(!$id>0){$id=$mysqli->insert_id;}
die(json_encode(array("success"=>true, "id"=>$id)));