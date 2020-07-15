<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
include("../../blocks/dates.php"); //Только для авторизованных

$id = intval($_GET['id']);
$id_user = $_COOKIE["user"];

/*

$q = $mysqli->query("SELECT
						hiking_invite.id,
						hiking.name,
						hiking.is_vacant_route,
						
						UNIX_TIMESTAMP(hiking.start) +{$time_offset} AS start, 
						UNIX_TIMESTAMP(hiking.finish)+{$time_offset} AS finish,
						CONCAT(users.name,' ', users.surname) AS user,
						UNIX_TIMESTAMP(hiking_invite.date_create)+{$time_offset} AS date,
						hiking_types.name AS type,
						hiking.id_route
					FROM hiking_invite 
						LEFT JOIN users ON users.id = hiking_invite.id_user_from
						LEFT JOIN hiking ON hiking.id = hiking_invite.id_hiking
						LEFT JOIN hiking_types ON hiking_types.id = hiking.id_type
						
					WHERE 
						
						hiking_invite.id_hiking={$id} AND
						hiking_invite.is_confirm = 0 AND 
						hiking_invite.id_user_to={$id_user}
					LIMIT 1");
if($q && $q->num_rows===1){
$r = $q->fetch_assoc();
		$r['start_date_rus'] = smartDate($r['start']);
		$r['finish_date_rus'] = smartDate($r['finish']);
		$r['duration'] = round((($r['finish']-$r['start'])/86400),1);
$r['smart_date'] = smartDate($r['date']);
exit(json_encode(array("error"=>"no_confirm", "invite"=>$r)));
} 

$q = $mysqli->query("SELECT id FROM hiking_members WHERE id_hiking={$id} AND id_user={$id_user} LIMIT 1");
if(!$q || $q->num_rows===0){exit(json_encode(array("error"=>"no_member")));}

*/

		
$q = $mysqli->query("
						SELECT 
							hiking.id, 
							hiking.id_type, 
							hiking.name, 
							hiking.bg, 
							hiking.`desc`,
							hiking.id_route,
							hiking.vk_group_id,
							hiking.is_vacant_route,
							hiking.color,
							hiking.id_region,
							geo_regions.name AS region_name,
							UNIX_TIMESTAMP(hiking.start) +{$time_offset} AS start, 
							UNIX_TIMESTAMP(hiking.finish) +{$time_offset} AS finish,
							hiking_types.name AS type,
							(SELECT count(`id`) FROM `hiking_members` WHERE `id_user`={$id_user} AND `id_hiking` = hiking.id ) AS member,
							(SELECT `is_admin` FROM `hiking_members` WHERE `id_user`={$id_user} AND `id_hiking` = hiking.id  LIMIT 1) AS admin,
							hiking.id_author = {$id_user}  AS author,
							(SELECT id_user={$id_user} FROM `hiking_editors` WHERE `id_user`={$id_user} AND `id_hiking` = hiking.id  LIMIT 1) AS editor,
							(SELECT COUNT(id) FROM `user_food_pref` WHERE `id_user`={$id_user}) AS food_pref_count
						FROM `hiking`
							LEFT JOIN hiking_types ON hiking_types.id = hiking.id_type
							LEFT JOIN geo_regions ON geo_regions.id = hiking.id_region
						WHERE hiking.id = {$id}
						ORDER BY hiking.start DESC, hiking.id_type
");
if($q){

		
		$result = array();
		$r = $q->fetch_assoc();
		$r["hikers"] = array();
		$r["is_i_hiker"] = false;
	
		$qi = $mysqli->query("SELECT
			hiking_members.id_user, UNIX_TIMESTAMP(hiking_members.date) AS date ,
			users.name, users.surname, 
			users.vk_id,
			users.photo_50, users.photo_100, users.sex
		FROM hiking_members LEFT JOIN users ON hiking_members.id_user = users.id
		WHERE hiking_members.id_hiking={$id} ORDER BY hiking_members.date");
								
			while($ri = $qi->fetch_assoc()){
				$r["hikers"][] = $ri;
				if($ri['id_user']==$id_user){ $r["is_i_hiker"]=true;}
			}
		

			$qi = $mysqli->query("SELECT
			positions.name,
			positions.description,
			hiking_vacancies.id_position,
			hiking_vacancies.comment,
			hiking_vacancies_response.date,
			hiking_vacancies_response.approve_date,
			hiking_vacancies_response.id_user,
			CONCAT(users.name,' ', users.surname) as approver
		FROM hiking_vacancies_response
		LEFT JOIN hiking_vacancies ON hiking_vacancies.id = hiking_vacancies_response.id_hiking_vacancy
		LEFT JOIN positions ON positions.id = hiking_vacancies.id_position
		LEFT JOIN users ON users.id = hiking_vacancies_response.approve_user_id
		WHERE hiking_vacancies.id_hiking={$id} AND hiking_vacancies_response.approve_user_id IS NOT NULL");
				if(!$qi){die($mysqli->error);}				
			while($ri = $qi->fetch_assoc()){
				for($i = 0; $i<count($r["hikers"]); $i++ ){
					if(!isset($r["hikers"][$i]['positions'])) {
						$r["hikers"][$i]['positions'] = array();
					}
					if($ri['id_user'] == $r["hikers"][$i]['id_user']) {
						$r["hikers"][$i]['positions'][] = $ri;
					}
				}
			}

		
		$r['start_date_rus'] = smartDate($r['start']);
		$r['finish_date_rus'] = smartDate($r['finish']);
		
		$r['duration'] = round((($r['finish']-$r['start'])/86400),1);

		
		$r['start_date'] = date('Y-m-d H:i:s', $r['start']);
		$r['finish_date'] = date('Y-m-d H:i:s',$r['finish']);
		$r['timeout'] = $r['finish']<time();

		echo json_encode($r);
}else{exit(json_encode(array("error"=>"Не могу получить список походов \r\n".$mysqli->error)));}

?>