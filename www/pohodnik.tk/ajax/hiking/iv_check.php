<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id = intval($_GET['id']);
$id_user = $_COOKIE["user"];
$res = array();
$q = $mysqli->query("SELECT iv.id, iv.name, (SELECT count(iv_ans.id) FROM iv_ans LEFT JOIN iv_qq ON iv_ans.id_qq = iv_qq.id WHERE iv_qq.id_iv = iv.id AND iv_ans.id_user={$id_user}) AS cou FROM `iv`

 WHERE iv.id_hiking = {$id} AND iv.main=1 LIMIT 1");
 if($q && $q->num_rows>0){
	while($r= $q->fetch_assoc()){
		if($r['cou']==0){ $res[] = $r; }
	}
 }
 
 echo json_encode($res);