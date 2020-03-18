<?php
include("../../blocks/db.php"); //подключение к БД
$result=array(); $id_user = $_COOKIE["user"];
$q = $mysqli->query("SELECT users.id, users.name, users.surname, users.photo_100 AS photo, COUNT( hiking_radish.id ) AS cou, UNIX_TIMESTAMP( MAX( hiking_radish.date ) ) AS uts
FROM  `hiking_radish` 
LEFT JOIN users ON hiking_radish.id_user = users.id
GROUP BY hiking_radish.id_user
ORDER BY cou DESC, uts
");
if(!$q){die($mysqli->error);}
$result['users']= array();
while($r = $q->fetch_assoc()){
$r['date'] = date('d.m.Y H:i',$r['uts']);
	$result['users'][] = $r;
}

$q = $mysqli->query("SELECT DISTINCT comment FROM `hiking_radish`");
$result['comments']= array();
while($r = $q->fetch_row()){
$result['comments'][] = $r[0];
}

echo json_encode($result);