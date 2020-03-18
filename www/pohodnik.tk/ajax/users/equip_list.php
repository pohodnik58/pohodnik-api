<?
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$add_q = "";
if(isset($_GET['flag']) && in_array($_GET['flag'], array('is_group','is_musthave'))){
	$add_q = " AND `".$_GET['flag']."`=1 ";
}
$res = array();
$id_user = isset($_GET['id_user'])?intval($_GET['id_user']):$_COOKIE["user"];
 $q = $mysqli->query("SELECT `id`, `name`, `weight`, `value`, `is_musthave`, `is_group`,`photo`, id_parent FROM `user_equip` WHERE `id_user`={$id_user} {$add_q} ORDER BY id_parent, is_group DESC, is_musthave, name");
 
 if($q && $q->num_rows>0){
		while($r = $q->fetch_assoc()){
			$res[] = $r;
		}
}

die(json_encode($res));