<?
include("../../blocks/db.php"); //подключение к БД
if(!isset($_GET['uts'])){die("TIME :(");}
$q = $mysqli->query("SELECT lat, lon, DATE(timestamp) as date, unix_timestamp(timestamp) AS uts FROM user_gps WHERE id_user = ".intval($_GET['id_user'])." AND unix_timestamp(timestamp)> ".intval($_GET['uts'])." ORDER BY timestamp");
$res = array();
if(!$q){die($mysqli->error);}
while($r=$q->fetch_assoc()){
	$res[] = $r;
}
echo json_encode($res);
?>