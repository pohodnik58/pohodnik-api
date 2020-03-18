<?php
include("../../blocks/db.php");
$res = array();
$more = "";
if(isset($_GET['full'])){
	$more = ", paths";
}
$q = $mysqli->query("SELECT `id`, `name`, `viewBox` ".$more." FROM `route_icons_svg`");
while($r = $q->fetch_assoc()){
	$vba = explode(' ',$r['viewBox']);
	$r['delta'] = floatval($vba[2])/floatval($vba[3]);
	$res[] = $r;
}
die(json_encode($res));