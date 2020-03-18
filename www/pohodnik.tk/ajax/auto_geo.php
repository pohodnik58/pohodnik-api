<?php
function getRealIpAddr() {
      if (!empty($_SERVER['HTTP_CLIENT_IP']))
      { $ip=$_SERVER['HTTP_CLIENT_IP']; }
      elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
      {$ip=$_SERVER['HTTP_X_FORWARDED_FOR']; }
      else {$ip=$_SERVER['REMOTE_ADDR']; }
      return $ip;
}

$ip = getRealIpAddr();

include_once("lib/SxGeo22_API/SxGeo.php"); 

$SxGeo = new SxGeo('lib/SxGeo22_API/SxGeoCity.dat', SXGEO_BATCH | SXGEO_MEMORY); // Самый быстрый режим 


$gorod  = $SxGeo->getCityFull($ip);
$regname = $gorod['region']['name_ru'];
if(strlen($regname)>0){
	include_once('../blocks/db.php');
	$q = $mysqli->query("SELECT id, name, name_r FROM geo_regions WHERE name LIKE('%{$regname}%') LIMIT 1");
	if($q && $q->num_rows===1){
		die(json_encode($q->fetch_assoc()));
	}
}


?>