<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
include("../../blocks/dates.php"); //Только для авторизованных
$mode = isset($_GET['mode'])?$mysqli->real_escape_string($_GET['mode']):'current';
$type = isset($_GET['type'])?$mysqli->real_escape_string($_GET['type']):'my';
$id_user = $_COOKIE["user"];
$claus ="1";
switch($mode){
	case 'current': // Текущие походы
		$claus .= " AND hiking.start>='".date('Y-m-d H:i:s')."' ";
	break;	
	case 'old': // Архив
		$claus .= " AND hiking.finish<'".date('Y-m-d H:i:s')."' ";
	break;
	
}
switch($type){
	case 'my': // мои
		$claus .= " AND (SELECT count(`id`) FROM `hiking_members` WHERE `id_user`={$id_user} AND `id_hiking` = hiking.id )=1 ";
	break;	
	case 'all': // все
		$claus .= " AND (SELECT count(`id`) FROM `hiking_members` WHERE `id_user`={$id_user} AND `id_hiking` = hiking.id )=0 ";
	break;
	
}		
	if(isset($_GET["actual"]) && $_GET["actual"]==1){
		$claus = "  hiking.start>='".date('Y-m-d H:i:s')."' ";
	}
	
	if(isset($_GET["admin"]) && $_GET["admin"]==1){
		$claus .= " AND hiking.id_author={$id_user} ";
	}	
	

if(isset($_GET['id'])){
	if(is_array($_GET['id'])){
		$claus = " hiking.id IN(".implode(',',$_GET['id']).") ";
	} else if($_GET['id']>0){
		$claus = " hiking.id = ".$_GET['id'];
	}
}
	
$q = $mysqli->query("
						SELECT 
							hiking.id, 
							hiking.id_type, 
							hiking.name, 
							hiking.ava, 
							hiking.`desc`, 
							hiking.id_route,
							hiking.id_author={$id_user} AS iauthor,
							UNIX_TIMESTAMP(hiking.start)+{$time_offset} AS start, 
							UNIX_TIMESTAMP(hiking.finish)+{$time_offset} AS finish,
							hiking_types.name AS type,
							(SELECT count(`id`) FROM `hiking_members` WHERE `id_hiking` = hiking.id ) AS members_count,
							(SELECT count(`id`) FROM `hiking_members` WHERE `id_user`={$id_user} AND `id_hiking` = hiking.id ) AS member,
							(SELECT `is_admin` FROM `hiking_members` WHERE `id_user`={$id_user} AND `id_hiking` = hiking.id ) AS admin
						FROM `hiking`
							LEFT JOIN hiking_types ON hiking_types.id = hiking.id_type
						WHERE {$claus}
						ORDER BY hiking.start DESC, hiking.id_type
");
if($q){
	$result = array();
	while($r = $q->fetch_assoc()){
		$r['start_date_rus'] = smartDate($r['start']);
		$r['finish_date_rus'] = smartDate($r['finish']);
		
		$r['duration'] = round((($r['finish']-$r['start'])/86400),1);

		
		$r['start_date'] = date('Y-m-d H:i:s', $r['start']);
		$r['finish_date'] = date('Y-m-d H:i:s',$r['finish']);
		
		$result[] = $r;
	}

	
	echo json_encode($result);
}else{exit(json_encode(array("error"=>"Не могу получить список походов \r\n".$mysqli->error)));}

?>