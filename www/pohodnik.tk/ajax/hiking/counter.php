<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных

$id_user = $_COOKIE["user"];
$result = array("my_current"=>0, "my_other"=>0, "other_current"=>0, "other_other"=>0);		
$q = $mysqli->query("SELECT COUNT(id) FROM `hiking` WHERE hiking.start>='".date('Y-m-d H:i:s')."'  AND (SELECT count(`id`) FROM `hiking_members` WHERE `id_user`={$id_user} AND `id_hiking` = hiking.id )=1   ");
if($q){$r = $q->fetch_row(); $result["my_current"] = $r[0];
}else{exit(json_encode(array("error"=>"Не могу получить кол-во походов my_current\r\n".$mysqli->error)));}

$q = $mysqli->query("SELECT COUNT(id) FROM `hiking` WHERE hiking.finish<'".date('Y-m-d H:i:s')."'   AND (SELECT count(`id`) FROM `hiking_members` WHERE `id_user`={$id_user} AND `id_hiking` = hiking.id )=1   ");
if($q){$r = $q->fetch_row(); $result["my_other"] = $r[0];
}else{exit(json_encode(array("error"=>"Не могу получить кол-во походов my_other\r\n".$mysqli->error)));}

$q = $mysqli->query("SELECT COUNT(id) FROM `hiking` WHERE hiking.start>='".date('Y-m-d H:i:s')."'  AND (SELECT count(`id`) FROM `hiking_members` WHERE `id_user`={$id_user} AND `id_hiking` = hiking.id )=0 ");
if($q){$r = $q->fetch_row(); $result["other_current"] = $r[0];
}else{exit(json_encode(array("error"=>"Не могу получить кол-во походов other_current\r\n".$mysqli->error)));}

$q = $mysqli->query("SELECT COUNT(id) FROM `hiking` WHERE  hiking.finish<'".date('Y-m-d H:i:s')."'  AND (SELECT count(`id`) FROM `hiking_members` WHERE `id_user`={$id_user} AND `id_hiking` = hiking.id )=0  ");
if($q){$r = $q->fetch_row(); $result["other_other"] = $r[0];
}else{exit(json_encode(array("error"=>"Не могу получить кол-во походов other_other\r\n".$mysqli->error)));}

echo json_encode($result);

?>