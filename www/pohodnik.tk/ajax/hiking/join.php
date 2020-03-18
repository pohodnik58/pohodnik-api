<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
include("../../blocks/dates.php"); //Только для авторизованных

$id_hiking = intval($_POST['id_hiking']);
$id_user = $_COOKIE["user"];
$res = array();

$q = $mysqli->query("SELECT id, name, `desc`, UNIX_TIMESTAMP(date_start) AS date_start, UNIX_TIMESTAMP(date_finish) AS date_finish, 
						(SELECT UNIX_TIMESTAMP(iv_ans.date) FROM iv_ans LEFT JOIN iv_qq ON iv_ans.id_qq=iv_qq.id WHERE iv_qq.id_iv=iv.id AND iv_ans.id_user={$id_user} LIMIT 1) AS date_complete 
					FROM iv WHERE id_hiking={$id_hiking}  AND main=1");
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
if($q && $q->num_rows>0){
	
	while($r=$q->fetch_assoc()){
		if(!$r['date_complete'] || !$r['date_complete']>0){ $res[] = $r; };
	}
}
if($q && $q->num_rows===0 || count($res)===0){
	$q = $mysqli->query("SELECT UNIX_TIMESTAMP(start) AS start, UNIX_TIMESTAMP(finish) AS finish FROM hiking WHERE id={$id_hiking} LIMIT 1");
	if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
	if($q->num_rows===1){
		$r = $q->fetch_assoc();
		if($r['start']<time()){die(json_encode(array("error"=>"Поход уже состоялся")));}
		if($mysqli->query("INSERT INTO hiking_members SET id_hiking={$id_hiking}, id_user={$id_user}, date=NOW()")){
			die(json_encode(array("success"=>true)));
		}else{
			die(json_encode(array("error"=>$mysqli->error)));
		}
	} else {
		die(json_encode(array("error"=>"NotFound")));
	}
} else {
	die(json_encode(($res)));
}