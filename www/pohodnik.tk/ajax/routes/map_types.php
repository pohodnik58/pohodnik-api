<?php
include("../../blocks/db.php"); //подключение к БД
$result = array();
$q = $mysqli->query("SELECT `id`, `name`, `key`, `tileUrlTmpl`, `isElliptical`, `subdomains`, `minZoom`, `maxZoom` FROM `route_maps` WHERE 1");
	if($q && $q->num_rows>0){
		while($r = $q->fetch_assoc()){
			$r['isElliptical'] = ($r['isElliptical']==1);
			$result[] = $r;
		}
		die( json_encode($result) );
	}else{
		die(json_encode(array("error"=>"Ошибка при получении данных. \r\n".$mysqli->error)));
	}
?>