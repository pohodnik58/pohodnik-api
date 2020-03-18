<?php
	include('../../../blocks/db.php');
	include("../../../blocks/for_auth.php"); //Только для авторизованных
	$id_user = $_COOKIE["user"];
	$q = $mysqli->query("SELECT 
		user_subscribes.id, 
		user_subscribes.id_user, 
		user_subscribes.email, 
		user_subscribes.confirm_code,
		user_subscribes.confirm_date,
		unix_timestamp(user_subscribes.confirm_date)>0 AS is_confirmed,
		user_subscribes.is_active,
		GROUP_CONCAT(DISTINCT CONCAT(geo_regions.id,'|',geo_regions.name) SEPARATOR ',') AS regions,
		GROUP_CONCAT(DISTINCT CONCAT(hiking_types.id,'|',hiking_types.name) SEPARATOR ',') AS types
	FROM `user_subscribes` 
		LEFT JOIN user_subscribes_regions ON user_subscribes_regions.id_subs = user_subscribes.id
		LEFT JOIN geo_regions ON geo_regions.id = user_subscribes_regions.id_region
		LEFT JOIN user_subscribes_types ON user_subscribes_types.id_subs = user_subscribes.id
		LEFT JOIN hiking_types ON hiking_types.id = user_subscribes_types.id_type
	WHERE user_subscribes.id_user={$id_user}");
	if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
	$res = array();


$func = function($value) {
	$tmp = explode('|', $value);
    return array('id'=>$tmp[0], 'name'=>$tmp[1]);
};

	while($r = $q -> fetch_assoc()){
		if(!$r['id']){continue;}
		$r['regions'] = strlen($r['regions'])>0?array_map($func, explode(',', $r['regions'])):array();
		$r['types'] = strlen($r['types'])?array_map($func, explode(',', $r['types'])):array();
		$res[]=$r;
	}

	die(json_encode($res));
?>