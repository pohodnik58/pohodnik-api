<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
include("../../blocks/dates.php"); //Только для авторизованных
$result = array();
$id_hiking = intval($_GET['id_hiking']);
$id_user = $_COOKIE["user"];
$q = $mysqli->query("SELECT `id`, `name`, `desc`, UNIX_TIMESTAMP(`date_start`) +{$time_offset} AS d1, UNIX_TIMESTAMP(`date_finish`) +{$time_offset} AS d2, `lat`, `lon` FROM `hiking_keypoints` WHERE id_hiking={$id_hiking} ORDER BY date_start");
if(!$q){die(json_encode(array("error"=>"Error. ".$mysqli->error)));}
while($r=$q->fetch_assoc()){
$r['start'] = array("date"=>date("d.m.Y",$r['d1']),"dateISO"=>date("Y-m-d",$r['d1']), "time"=>date("H:i",$r['d1']));
$r['finish'] = array("date"=>date("d.m.Y",$r['d2']),"dateISO"=>date("Y-m-d",$r['d2']), "time"=>date("H:i",$r['d2']));
$result[] = $r;
}

die(json_encode($result));
