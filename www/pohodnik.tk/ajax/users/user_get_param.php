<?php
	include("../../blocks/db.php"); //подключение к БД
	include("../../blocks/for_auth.php"); //Только для авторизованных
	$fields = explode(',',$mysqli->real_escape_string(trim($_GET['fields'])));
	$id_user = $_COOKIE["user"];
	for($i=0; $i<count($fields); $i++){
		if(!in_array($fields[$i],explode(',','id,email,name,surname,sex,dob,reg_date,ava,address,phone,skype,icq,skills,photo_50,photo_100,photo_200_orig,photo_max,photo_max_orig,uniq_code,vk_id,weight,growth,id_region'))){die(json_encode(array("error"=>$fields[$i]." no in white list"))); break;}
	}
	//exit(("SELECT ".implode(',',$fields)." FROM users WHERE id={$id_user}"));
	$q = $mysqli->query("SELECT ".implode(',',$fields)." FROM users WHERE id={$id_user}");
	if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
	die(json_encode($q->fetch_assoc()));
?>