<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
<style>

html, body { font-family: Arial; font-size:10px;}

table {
	border-collapse: collapse; /*убираем пустые промежутки между ячейками*/
	border: 1px solid grey; /*устанавливаем для таблицы внешнюю границу серого цвета толщиной 1px*/
}

th {
	border: 1px solid grey; 
	padding: 3px 4px;
	vertical-align: bottom;
	max-height:30px;
}

td {
	border: 1px solid grey;
	padding: 2px 3px;
}

</style>
<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id = intval($_GET['id']);
$id_user = $_COOKIE["user"];

$q = $mysqli->query("SELECT * FROM `users` WHERE `id` = {$id_user} AND admin=1 LIMIT 1");
if($q && $q->num_rows===1){
} else {
	exit(json_encode(array("error"=>"Access denied. #{$id_user}")));
}



$q = $mysqli->query("SELECT `id`, `name` FROM `iv` WHERE `id_hiking` = {$id} LIMIT 1");
if($q && $q->num_rows===1){
	$r = $q->fetch_row();
	$id_iv = $r[0];
	
echo "	<title>".$r[1]."</title>
</head>
<body>";
	
	$users = array();
	$q3 = $mysqli->query("	SELECT users.id,  users.name, users.surname, users.dob, users.sex, iv_ans.date 
							FROM `iv_ans`
								LEFT JOIN iv_qq ON(iv_qq.id=iv_ans.id_qq)
								LEFT JOIN users ON(users.id=iv_ans.id_user)
							WHERE iv_qq.id_iv = {$id_iv}
							ORDER BY  iv_ans.date DESC");
							
	if(!$q3){die($mysqli->error);}						
	while($r3 = $q3->fetch_assoc()){
		$r3['ans'] = array();
		$users[$r3['id']] = $r3;
	}
	
//exit(json_encode($users));	

	$thead_row1 = array();
	$thead_row2 = array();
	$qqq = $mysqli->query("SELECT id, id_type, is_require, name, text, id_type FROM iv_qq  
						 WHERE id_iv = {$id_iv}   ORDER BY order_index, id");
						

	while($qq = $qqq->fetch_assoc()){
		
		$thead_row1[$qq['id']]['count'] = 0;
		$thead_row1[$qq['id']]['name'] = $qq['name'];
		$thead_row1[$qq['id']]['id'] = $qq['id'];
	
	
	//	$thead_row2[$qq['id']][] = array('name' => "*");
	
		
	
		switch($qq['id_type']){
			case 1: // Ввод
				$thead_row2[$qq['id']][] = array('name' => "*", "type"=>$qq['id_type']); 
				$thead_row1[$qq['id']]['count']++;
			break;			
			case 2: // из списка
				
			
				$q = $mysqli->query("SELECT  id, `value` as name  FROM `iv_qq_params_variants` WHERE `id_qq`=".$qq['id']." ORDER BY order_index");
				if(!$q){exit(json_encode(array("error"=>"iv_qq_params_variants \r\n".$mysqli->error)));}
				while($r1=$q->fetch_assoc()){
					$thead_row1[$qq['id']]['count']++;
					$thead_row2[$qq['id']][] = array('name' => $r1['name'], "id"=>$r1['id'], "type"=>$qq['id_type']); 
				}

			break;			
			case 3: // справочник
				$q = $mysqli->query("SELECT iv_qq_params_dir.id, iv_qq_params_dir.id_dir,
									 iv_directories.`name`, iv_directories.`desc`, iv_directories.`table`
									 FROM `iv_qq_params_dir` 
										LEFT JOIN iv_directories ON(iv_directories.id = iv_qq_params_dir.id_dir)
									 WHERE  iv_qq_params_dir.id_qq =".$qq['id']." LIMIT 1");
				if(!$q){exit(json_encode(array("error"=>"iv_qq_params_dir \r\n".$mysqli->error)));}
				$qq['data'] = $q->fetch_assoc();


				$q = $mysqli->query("SELECT `name`, `value`, `is_equall` FROM `iv_directories_param` 
				WHERE `id_dir`=".$qq['data']['id_dir']." LIMIT 1");
				if(!$q){exit(json_encode(array("error"=>"iv_directories_param \r\n".$mysqli->error)));}
				$r0 = $q->fetch_assoc();
				$dir_fields = array();
				$q = $mysqli->query("SELECT id, name FROM `".$qq['data']['table']."` WHERE `".$r0['name']."`='".$r0['value']."'");
				if($q && $q->num_rows>0){
					while($r1 = $q->fetch_assoc()){
						$thead_row1[$qq['id']]['count']++;
						$thead_row2[$qq['id']][] = array('name' => $r1['name'], 'id' => $r1['id'], "type"=>$qq['id_type']); 
					}
				}

			break;
			
		}		
		
	}
	
	echo "<h1>".$r[1]."</h1>";
	echo "<table border=1 cellpadding=0 cellspacing=0>";
	echo "<thead>";
		echo '<tr>
				<th colspan="5">Основная информация</th>
			';
		
	foreach($thead_row1 as $v){
		
		echo "<th ".
					($v['count']>1?' colspan="'.$v['count'].'"':"").
					(($thead_row2[$v['id']][0]['name']=='*')?' rowspan=2 ':'').
			">".$v['name']."</th>";
	}
	echo '</tr>';

		echo '<tr>
		
		<th>Дата ответа</th>
		<th>ID</th>
		<th>Фамилия Имя</th>
		<th>Дата рождения</th>
		<th>Пол</th>

		';
		
	foreach($thead_row2  as $v){
		foreach($v as $vv){
		if($vv['name']!='*')
		echo "<th style=''>".$vv['name']."</th>";
		// -moz-transform: rotate(90deg); -webkit-transform: rotate(90deg);  -o-transform: rotate(90deg); writing-mode: tb-rl
		
		}
	}
	echo '</tr>';

	echo "</thead>";
	


	echo '<tbody>';
	
$q = $mysqli->query("SELECT DISTINCT
						users.id, 
						users.name,
						users.surname,
						users.dob,
						users.sex,
						iv_ans.id AS id_ans,
						iv_ans.date,
						iv_ans.id_qq 
					FROM 
						`iv_ans`
					LEFT JOIN users ON(users.id=iv_ans.id_user)
					LEFT JOIN iv_qq ON(iv_qq.id=iv_ans.id_qq)
						WHERE iv_qq.id_iv = {$id_iv}
					GROUP BY users.id  ORDER BY  iv_ans.date");	
	while($r = $q->fetch_assoc()){
		$values = array();
		$qi = $mysqli->query(" SELECT 
									iv_ans_content.id, 
									iv_ans_content.v_from_input,
									iv_ans_content.v_from_variants, 
									iv_ans_content.v_from_dir,
									iv_ans_content.v_custom,
									iv_ans.id_qq
								FROM `iv_ans_content` 
									LEFT JOIN iv_ans ON(iv_ans_content.id_ans=iv_ans.id) 
								WHERE iv_ans.id_user=".$r['id']."");
									if(!$qi){exit(json_encode(array("error"=>"iv_directories_param \r\n".$mysqli->error)));}
		while($qr = $qi->fetch_assoc()){
			if(!isset($values[$qr['id_qq']]))$values[$qr['id_qq']] = array();
			if(strlen($qr['v_from_input'])>0){
				$values[$qr['id_qq']][] = $qr['v_from_input'];
			} else if( $qr['v_from_variants']>0 ){
				$values[$qr['id_qq']][] = $qr['v_from_variants'];
			} else if( $qr['v_from_dir']>0 ){
				$values[$qr['id_qq']][] = $qr['v_from_dir'];
			}
		}
		
	echo "<tr>";
	echo "		
		<td>".$r["date"]."</td>
		<td>".$r["id"]."</td>
		<td>".$r["name"]."&nbsp;".$r["surname"]."</td>
		<td>".$r["dob"]."</td>
		<td>".($r["sex"]==1?"М":"Ж")."</td>
		";
		foreach($thead_row2 as $id_qq=>$v){
			foreach($v as $vv){
			
			// 
			
			
			
			echo "<td style=''>";
		//	print_r($vv);
		//	print_r($values[$id_qq]);
			
			if(isset($values[$id_qq])){
				if(isset($vv['id']) && in_array($vv['id'], $values[$id_qq])){
					echo "1";
				} else {
					if(count($values[$id_qq])===1 && $vv['type']==1){
						echo $values[$id_qq][0];
					} else {echo "0";}
					
				}
			}
			
			//echo isset(isset($vv['id']) &&  &&  in_array()?1":isset($values[$id_qq]) && strlen($values[$id_qq][0])>0?$values[$id_qq]) && strlen($values[$id_qq][0]:'-';
			echo "</td>";
			}
		}
		echo "</tr>";
	}
	echo '</tbody>';
	
	
	echo "</table>";
}else{exit(json_encode(array("error"=>"Нет доступного вам опроса с таким айдишником \r\n".$mysqli->error)));}

?>

</body>
</html>