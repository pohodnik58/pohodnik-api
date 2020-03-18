<?php
include("../blocks/db.php");

$id_user = $_COOKIE["user"];
$claus = $id_user>0?"( geo_regions.id = (SELECT id_region FROm users WHERE id={$id_user}) )":"1";
if(isset($_GET['q'])){
	$claus = " geo_regions.name LIKE('%".$mysqli->real_escape_string(trim($_GET['q']))."%') ";
	$af = ", ( geo_regions.id = (SELECT id_region FROm users WHERE id={$id_user}) ) AS my";
}
$q = $mysqli->query("SELECT geo_regions.id, geo_regions.name, geo_regions.id_country, geo_countries.name AS country_name {$af}
					 FROM geo_regions LEFT JOIN geo_countries ON geo_countries.id = geo_regions.id_country
					 WHERE {$claus}
					");
if(!$q){exit(json_encode(array("error"=>"Ошибка при получении данных пользователя. \r\n".$mysqli->error)));}
while($r = $q->fetch_assoc()){ $result[] = $r;}
echo json_encode($result);
?>