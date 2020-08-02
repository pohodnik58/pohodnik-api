<?php

	include("../../../blocks/db.php"); //подключение к БД
	include("../../../blocks/for_auth.php"); //Только для авторизованных
	include("../../../blocks/dates.php");
	$id_user = $_COOKIE["user"];
	$id_hiking = intval($_GET['id_hiking']);
	$addwhere = "";
	
	if(!($id_hiking>0)){die(json_encode(array("error"=>"id_hiking is undefined")));}

	$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_user} LIMIT 1");
	if($q && $q->num_rows===0){
		$q = $mysqli->query("SELECT id FROM hiking_editors WHERE id_hiking={$id_hiking}  AND is_financier=1  AND id_user = {$id_user} LIMIT 1");
		if($q && $q->num_rows===0){
			
			$q = $mysqli->query("SELECT id FROM hiking_members WHERE id_hiking={$id_hiking} AND id_user = {$id_user} LIMIT 1");
			if($q && $q->num_rows===0){
				die(json_encode(array("error"=>"Нет доступа")));
			}	
		}
	}

	if(isset($_GET['my'])){
		$addwhere .= " AND hiking_finance_payment.id_user={$id_user} ";
	}

	$res = array();
	$z = "SELECT
		hiking_finance_payment.*,
		UNIX_TIMESTAMP(hiking_finance_payment.date) AS uts,
		(hiking_finance_payment.id_user={$id_user}) AS my,
		users.name AS uname,
		users.surname as usurname
	FROM `hiking_finance_payment`
		LEFT JOIN users ON hiking_finance_payment.id_user = users.id
	WHERE
		hiking_finance_payment.id_hiking={$id_hiking}
		{$addwhere}
	ORDER BY my,date";
	
	$q = $mysqli->query($z);
	if(!$q ){exit(json_encode(array("error"=>"Ошибка\r\n".$mysqli->error."\r\n".$z)));}
	while($r = $q->fetch_assoc()){
		$res[] = $r;
	}
	
	echo json_encode($res);