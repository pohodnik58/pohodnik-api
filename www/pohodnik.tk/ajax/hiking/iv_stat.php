<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id = intval($_GET['id']);
$id_user = $_COOKIE["user"];
$q = $mysqli->query("SELECT `id`, `name` FROM `iv` WHERE `id_hiking` = {$id} LIMIT 1");
if($q && $q->num_rows===1){
	$r = $q->fetch_row();
	$id_iv = $r[0];
	
	$qqq = $mysqli->query("SELECT id, id_type, is_require, name, text, id_type FROM iv_qq  
						 WHERE id_iv = {$id_iv}  AND is_private=0 ORDER BY order_index, id");
	while($qq = $qqq->fetch_assoc()){		
		switch($qq['id_type']){
			case 1: // Ввод
				$q = $mysqli->query("
							SELECT iv_ans_content.v_from_input as name, COUNT(iv_ans_content.id) AS c 
							FROM iv_ans_content  LEFT JOIN iv_ans ON iv_ans.id = iv_ans_content.id_ans WHERE iv_ans.`id_qq`=".$qq['id']." GROUP BY iv_ans_content.v_from_input
						");
				if(!$q){exit(json_encode(array("error"=>"iv_qq_params_input \r\n".$mysqli->error)));}
				while($r2 = $q->fetch_assoc()){
					 $qq['data'][] = $r2;
				}
			
			break;			
			case 2: // из списка
				
			
				$q = $mysqli->query("SELECT `id`, `value` as name, (SELECT COUNT(iv_ans_content.id) 
					 FROM iv_ans_content 
					 LEFT JOIN iv_ans ON iv_ans.id = iv_ans_content.id_ans
					 WHERE iv_ans_content.v_from_variants = iv_qq_params_variants.id AND iv_ans.id_qq = ".$qq['id'].") AS c FROM `iv_qq_params_variants` WHERE `id_qq`=".$qq['id']." ORDER BY order_index");
				if(!$q){exit(json_encode(array("error"=>"iv_qq_params_variants \r\n".$mysqli->error)));}
				while($r1=$q->fetch_assoc()){$qq['data'][] = $r1;}
				$qcust  =$mysqli->query("SELECT GROUP_CONCAT(iv_ans_content.v_custom) as cust
					 FROM iv_ans_content 
					 LEFT JOIN iv_ans ON iv_ans.id = iv_ans_content.id_ans
					 WHERE iv_ans.id_qq = ".$qq['id']." AND LENGTH(iv_ans_content.v_custom)>0 GROUP BY iv_ans.id_qq");
				$r3 = $qcust->fetch_assoc();
				$qq['cust'] = $r3['cust'];
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
				$q = $mysqli->query("SELECT id, name,
				(SELECT COUNT(iv_ans_content.id) 
					 FROM iv_ans_content 
					 LEFT JOIN iv_ans ON iv_ans.id = iv_ans_content.id_ans
					 WHERE iv_ans_content.v_from_dir = `".$qq['data']['table']."`.id  AND iv_ans.id_qq = ".$qq['id'].") AS c
					 

				
				FROM `".$qq['data']['table']."` WHERE `".$r0['name']."`='".$r0['value']."'");
				if($q && $q->num_rows>0){
					while($r1 = $q->fetch_assoc()){
						$dir_fields[] = $r1;
					}
				}
				
				$qq['data'] = $dir_fields;
				
				$qcust  =$mysqli->query("SELECT GROUP_CONCAT(iv_ans_content.v_custom) as cust
					 FROM iv_ans_content 
					 LEFT JOIN iv_ans ON iv_ans.id = iv_ans_content.id_ans
					 WHERE iv_ans.id_qq = ".$qq['id']." AND LENGTH(iv_ans_content.v_custom)>0 GROUP BY iv_ans.id_qq");
				$r3 = $qcust->fetch_assoc();
				$qq['cust'] = $r3['cust'];
				
				
			break;
			
		}		
		
		
		
		
		$result[] = $qq;
	}
	echo json_encode($result);
}else{exit(json_encode(array("error"=>"Нет доступного вам опроса с таким айдишником \r\n".$mysqli->error)));}

?>