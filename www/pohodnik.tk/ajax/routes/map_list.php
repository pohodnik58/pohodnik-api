<?php
include("../../blocks/db.php");//подключение к БД
$result = array();
$id_user = isset($_COOKIE["user"])?$_COOKIE["user"]:0;
$clous = "";
if(isset($_GET['region'])){
	$clous .= " AND geo_regions.id=".intval($_GET['region']);

}
$q = $mysqli->query("
						SELECT 
						routes.id, 
						routes.name, 
						routes.desc,
						routes.length, 
						routes.date_create,
						{$id_user} IN (SELECT id_user FROM route_editors WHERE id_route = routes.id) AS ieditor,
						routes.id_author = {$id_user} AS iauthor,
						UNIX_TIMESTAMP(routes.date_create) AS uts_date_create,
						routes.id_author,
						users.name AS username,
						users.surname,
						users.photo_50,
						GROUP_CONCAT( DISTINCT hiking.id ) AS hiking_id,
						SUM( IF(route_objects.is_in_distance=1, route_objects.distance, 0) ) AS dist,
						GROUP_CONCAT(DISTINCT CONCAT(editor.id,'|',editor.name,' ',editor.surname,'|',editor.photo_50)) AS editors,
						GROUP_CONCAT(DISTINCT CONCAT(geo_regions.id,'|',geo_regions.name)) AS regions
						FROM `routes` 
							LEFT JOIN users ON(users.id = routes.id_author)
							LEFT JOIN route_objects ON route_objects.id_route=routes.id
							LEFT JOIN route_editors ON route_editors.id_route=routes.id
                            LEFT JOIN users AS editor ON route_editors.id_user=editor.id
                            LEFT JOIN route_regions ON route_regions.id_route=routes.id
                            LEFT JOIN geo_regions ON route_regions.id_region=geo_regions.id
                            LEFT JOIN hiking ON hiking.id_route = routes.id
                        WHERE 1 {$clous}
						GROUP BY routes.id ORDER BY COUNT(DISTINCT  hiking.id) DESC, routes.date_create

	");
	if($q && $q->num_rows>0){
		while($r = $q->fetch_assoc()){
			$r['date_create_rus'] = date('d.m.Y', $r['uts_date_create']);
			$result[] = $r;
		}
		echo json_encode($result);
	}else{
		exit(json_encode(array("error"=>"Ошибка при получении данных пользователя. \r\n".$mysqli->error)));
	}

?>