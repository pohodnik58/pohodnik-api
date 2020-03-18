<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();

$id = intval($_GET['id']);

if(!$id>0){die(json_encode(array('bad'=>"Undefined ID")));}

$id_user = $_COOKIE["user"];
$bad = array();
$count_finished_ans = 0;
$exist_my_ans = false;

$q = $mysqli->query("SELECT UNIX_TIMESTAMP(iv_ans.date) as time FROM `iv_ans` LEFT JOIN iv_qq ON iv_ans.id_qq = iv_qq.id
						WHERE iv_qq.id_iv={$id} AND iv_ans.`id_user`={$id_user}
						ORDER BY iv_ans.date DESC LIMIT 1");

if($q && $q->num_rows===1){
	$r = $q->fetch_row();
	$bad[] = "Вы уже ответили на данный опрос (".date('d.m.Y',$r[0]).")";
	$exist_my_ans = true;
}



$q = $mysqli->query("SELECT id FROM iv_ans LEFT JOIN iv_qq ON iv_ans.id_qq=iv_qq.id WHERE iv_qq.id_iv={$id} LIMIT 1");
if($q){ $count_finished_ans = $q->num_rows; }

$q = $mysqli->query("SELECT `name`, `desc`, 
							UNIX_TIMESTAMP(`date_start`) AS date_start,  
							UNIX_TIMESTAMP(`date_finish`) AS date_finish,
							`hello_text`, by_text, `members_limit` FROM `iv` WHERE `id`={$id} LIMIT 1");

if($q && $q->num_rows===1){

	$r = $q->fetch_assoc();
	if($r['date_start']>time()){
		$bad[] = "Опрос еще не начался. Дата начала ".date('d.m.Y в H:i',$r['date_start'])."";
	}

	if($r['date_finish']<time()){
		$bad[] = "Опрос завершился ".date('d.m.Y в  H:i',$r['date_finish'])." и сейчас не доступен.";
		$r['deadline_old'] = true;
	}

	if($r['members_limit']!=0 && $r['members_limit']<=$count_finished_ans){
		$bad[] = "Полна коробочка. Опрос закрыт.";
	}	

	$r['finished'] = $count_finished_ans;
	$r['i_finish'] = $exist_my_ans;
	$result["ok"] = $r;
	
}	
$result["bad"] = $bad;
echo json_encode($result);




?>