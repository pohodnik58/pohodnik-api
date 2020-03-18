<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
include("../../blocks/dates.php"); //Только для авторизованных

$id_friend =0;
$id_user = $_COOKIE["user"];
if(isset($_GET['user'])){$id_user = intval($_GET['user']);}
if(isset($_GET['friend'])){$id_friend = intval($_GET['friend']);}

if( isset($id_friend) && $id_friend>0 ){
	$friend_hikings = array();
	$q = $mysqli->query("SELECT id_hiking FROM hiking_members WHERE id_user={$id_friend}");
	if($q && $q->num_rows>0){
		while($r = $q->fetch_row()){$friend_hikings[]=$r[0];}
	}
}


$q = $mysqli->query("
						SELECT 
							hiking.id, 
							hiking.id_type, 
							hiking.name, 
							hiking.`desc`, 
							hiking.id_route,
							hiking.id_author,
							hiking.color,
							UNIX_TIMESTAMP(hiking.start)+{$time_offset} AS start, 
							UNIX_TIMESTAMP(hiking.finish)+{$time_offset} AS finish,
							hiking_types.name AS type,
							({$id_user} IN (SELECT id_user FROM hiking_editors WHERE id_hiking=hiking.id)) AS ieditor,
							({$id_user} = hiking.id_author) AS iauthor,
							(SELECT SUM(route_objects.distance) FROM route_objects WHERE id_route = hiking.id_route AND is_confirm=1) AS distance
							
						FROM `hiking`
							LEFT JOIN hiking_members ON hiking_members.id_hiking = hiking.id
							LEFT JOIN hiking_types ON hiking_types.id = hiking.id_type
						WHERE 
							hiking_members.id_user = {$id_user} ".(isset($friend_hikings)?" AND hiking_members.id_hiking IN(".implode(",",$friend_hikings).") ":"")."
						ORDER BY hiking.start DESC, hiking.id_type
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