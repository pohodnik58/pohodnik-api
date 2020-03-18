<?
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных

$res = array();
$id_user = $_COOKIE["user"];
 $q = $mysqli->query("SELECT hiking_radish.id,  hiking_radish.id_hiking, hiking_radish.comment, hiking_radish.date, hiking_radish.killer, hiking.name FROM `hiking_radish` LEFT JOIN hiking ON hiking_radish.id_hiking=hiking.id  WHERE hiking_radish.id_user={$id_user} AND hiking_radish.comment=''");
 
 if($q && $q->num_rows>0){
		while($r = $q->fetch_assoc()){
			$res[] = $r;
		}
}

die(json_encode($res));