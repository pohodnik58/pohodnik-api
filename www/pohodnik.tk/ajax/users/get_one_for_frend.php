<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$id_user = $_COOKIE["user"];
$id = intval($_GET['id']);
$q = $mysqli->query("SELECT  
    COUNT(DISTINCT hiking_members.id_hiking) AS cou,
    MAX(UNIX_TIMESTAMP(hiking.start)) AS date, 
	MIN(UNIX_TIMESTAMP(hiking.finish)) AS start_date, 
	GROUP_CONCAT(DISTINCT hiking_members.id_hiking) AS hikings,
    users.id, 
    users.id={$id_user} AS i,
	users.name, 
    users.surname,  
    users.vk_id, 
    users.photo_50, 
    users.photo_100, 
    users.sex
FROM hiking_members 

LEFT JOIN users ON hiking_members.id_user = users.id
LEFT JOIN hiking ON hiking_members.id_hiking = hiking.id

WHERE hiking_members.id_hiking IN (SELECT id_hiking FROM hiking_members WHERE id_user={$id_user})  AND  users.id={$id} GROUP BY users.id  ORDER BY cou DESC, date");
if(!$q){exit(json_encode(array("error"=>"Не могу получить список походов \r\n".$mysqli->error)));}
if($q->num_rows===0){exit(json_encode(array("error"=>"no frend", "cou"=>0)));}



	$result = array();
	while($r = $q->fetch_assoc()){
		$r['date_rus'] = date('d.m.Y', $r['date']);
		$r['date_rus_start'] = date('d.m.Y', $r['start_date']);
		$result[] = $r;
	}
	echo json_encode($result);


?>