<?php
include("../../blocks/db.php"); //подключение к БД
//include("../../blocks/for_auth.php"); //Только для авторизованных
$id_user = isset($_COOKIE["user"])? $_COOKIE["user"]:0;
$id = intval($_GET['id']);
$result = array();
$q = $mysqli->query("SELECT routes.`id`, routes.`name`, routes.preview_img, routes.`desc`, routes.`center_coordinates`, routes.`zoom`, routes.`length`, 
					routes.`id_author`,routes.`id_author`={$id_user} AS iauthor, routes.`id_type`,  routes.`date_create`
					FROM `routes` WHERE routes.id={$id} LIMIT 1");
if($q && $q->num_rows===1){

$result['route'] = $q->fetch_assoc();

if($result['route']['id_type']>0){
	$q = $mysqli->query("SELECT `id`, `name`, `key`, `tileUrlTmpl`, `isElliptical`, `subdomains`, `minZoom`, `maxZoom` FROM `route_maps` WHERE id=".$result['route']['id_type']." LIMIT 1");
	$result['type'] = $q->fetch_assoc();
}


	$q = $mysqli->query("SELECT geo_regions.* FROM `route_regions` 
							LEFT JOIN geo_regions ON geo_regions.id=route_regions.id_region WHERE route_regions.id_route={$id}");
	$result['regions'] = array();
	while($r =  $q->fetch_assoc()){
		$result['regions'][]=$r;
	}


echo json_encode($result);
}else{exit(json_encode(array("error"=>"Ошибка при получении данных. \r\n".$mysqli->error)));}

?>