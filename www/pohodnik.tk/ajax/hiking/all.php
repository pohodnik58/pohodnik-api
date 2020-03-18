<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
include("../../blocks/dates.php"); //Только для авторизованных

$id_user = $_COOKIE["user"];

$q = $mysqli->query("
						SELECT 
							hiking.id, 
							hiking.id_type, 
							hiking.name, 
							hiking.ava, 
							hiking.`desc`, 
							hiking.id_route,
							hiking.id_author,
							hiking.color,
							UNIX_TIMESTAMP(hiking.start)+{$time_offset} AS start, 
							UNIX_TIMESTAMP(hiking.finish)+{$time_offset} AS finish,
							hiking_types.name AS type,
							({$id_user} IN (SELECT id_user FROM hiking_editors WHERE id_hiking=hiking.id)) AS ieditor,
							({$id_user} = hiking.id_author) AS iauthor
							
						FROM `hiking`
							LEFT JOIN hiking_types ON hiking_types.id = hiking.id_type
						WHERE hiking.finish>'".date('Y-m-d H:i:s')."'
						ORDER BY hiking.start, hiking.id_type
");



if($q){
	$result = array();
	while($r = $q->fetch_assoc()){
		$r['start_date_rus'] = smartDate($r['start']);
		$r['finish_date_rus'] = smartDate($r['finish']);
		
		$r['duration'] = round((($r['finish']-$r['start'])/86400),1);
		$r['avai_edit'] = $r['id_author']===$id_user;
		
		$r['start_date'] = date('Y-m-d H:i:s', $r['start']);
		$r['finish_date'] = date('Y-m-d H:i:s',$r['finish']);
		
		$result[] = $r;
	}

	
	echo json_encode($result);
}else{exit(json_encode(array("error"=>"Не могу получить список походов \r\n".$mysqli->error)));}

?>