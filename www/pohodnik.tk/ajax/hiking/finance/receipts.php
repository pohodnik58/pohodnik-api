<?php

	include("../../../blocks/db.php"); //подключение к БД
	include("../../../blocks/for_auth.php"); //Только для авторизованных
	include("../../../blocks/dates.php");
	$id_user = $_COOKIE["user"];
	$id_hiking = intval($_GET['id_hiking']);
	$addwhere = "";

	if(isset($_GET['id_hiking'])){
		$id = intval($_GET['id_hiking']);
		$addwhere .= " AND hiking_finance_receipt.id_hiking={$id_hiking} ";
	}
	if(isset($_GET['my'])){
		$addwhere .= " AND hiking_finance_receipt.id_user={$id_user} ";
	}
	if(isset($_GET['id'])){
		$id = intval($_GET['id']);
		$addwhere .= " AND hiking_finance_receipt.id={$id} ";
	}

	$res = array();
	$q = $mysqli->query("SELECT
		hiking_finance_receipt.*,
		UNIX_TIMESTAMP(hiking_finance_receipt.date)  AS uts,
		(hiking_finance_receipt.id_user={$id_user}) AS my,
		users.name AS uname,
		users.surname as usurname,
		SUM(hiking_finance.cost * hiking_finance.amount) AS cursumm,
		GROUP_CONCAT(
			CONCAT(
				hfrp.id_user, '|', who.name, ' ', who.surname, '|', hfrp.date_create
			)
		) AS participation
	FROM
		`hiking_finance_receipt`
		LEFT JOIN users ON hiking_finance_receipt.id_user = users.id
		LEFT JOIN hiking_finance ON hiking_finance.id_receipt = hiking_finance_receipt.id
		LEFT JOIN hiking_finance_receipt_participation AS hfrp ON hfrp.id_hiking_receipt = hiking_finance_receipt.id
		LEFT JOIN users as who ON hfrp.id_user = who.id
	WHERE 1 {$addwhere}
	GROUP BY hiking_finance_receipt.id
	ORDER BY my, date
	");

	if(!$q ){exit(json_encode(array("error"=>"Ошибка\r\n".$mysqli->error)));}
	while($r = $q->fetch_assoc()){
		$r['date_rus'] = date('d.m.Y в H:i',$r['uts']-3600);
		$res[] = $r;
	}
	
	echo json_encode($res);