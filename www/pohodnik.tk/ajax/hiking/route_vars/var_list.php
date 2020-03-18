<?php
include("../../../blocks/db.php"); //подключение к БД
include("../../../blocks/for_auth.php"); //Только для авторизованных
include("../../../blocks/err.php"); //Только для авторизованных
$id_user = $_COOKIE["user"];
$id_hiking = intval($_GET['id_hiking']);

if(!($id_hiking>0)){die(json_encode(array("error"=>"id_hiking is undefined")));}
$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_user} LIMIT 1");
if($q && $q->num_rows===0){
	$q = $mysqli->query("SELECT id FROM hiking_members WHERE id_hiking={$id_hiking} AND id_user = {$id_user} LIMIT 1");
	if($q && $q->num_rows===0){
		die(json_encode(array("error"=>"Нет доступа")));
	}
}

$q = $mysqli->query("
	SELECT 

		hiking_route_variants.date AS date_create,
		CONCAT( users.name, ' ', users.surname ) AS author_name,
		{$id_hiking} AS hiking_id,
		routes.id AS route_id,
		routes.name AS route_name,
		routes.`desc` AS route_desc,
		routes.center_coordinates AS route_center,
		routes.zoom AS route_zoom,
		routes.preview_img AS route_preview,
		route_maps.tileUrlTmpl,
		route_maps.isElliptical,
		route_maps.subdomains,
		route_maps.name AS map_name,
		route_maps.key AS map_key,
		(SELECT COUNT(*) FROM hiking_route_variants_vote WHERE id_variant = hiking_route_variants.id ) AS vote_count,
		(SELECT COUNT(*) FROM hiking_route_variants_vote WHERE id_variant = hiking_route_variants.id AND id_user={$id_user} ) AS my_vote,
		{$id_user} = users.id AS my,
		SUM(route_objects.distance) AS distance
	FROM 
		hiking_route_variants
		LEFT JOIN users ON users.id = hiking_route_variants.id_author
		LEFT JOIN routes ON routes.id = hiking_route_variants.id_route
		LEFT JOIN route_maps ON route_maps.id = routes.id_type
		LEFT JOIN route_objects ON route_objects.id_route = routes.id AND route_objects.is_in_distance=1
	WHERE 
		hiking_route_variants.id_hiking = {$id_hiking}
	GROUP BY routes.id
	ORDER BY hiking_route_variants.date
");
if(!$q){die(json_encode(array("error"=>"Ji ".$mysqli->error)));}
$res = array();
while($r=$q->fetch_assoc()){
	$res[] = $r;
}

die(json_encode($res));