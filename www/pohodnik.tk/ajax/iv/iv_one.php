<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id = intval($_GET['id']);
$id_user = $_COOKIE["user"];


	$qb = $mysqli->query("SELECT id, name, `text`, `id_type`, `is_custom`, `is_require`, `is_multi` 
							FROM `iv_qq` WHERE `id_iv`={$id} ORDER BY order_index, id");
	if(!$qb){exit(json_encode(array("error"=>"iv_qq \r\n".$mysqli->error)));}
	while($r=$qb->fetch_assoc()){
		switch($r['id_type']){
			case 1: // Ввод
				$q = $mysqli->query("	SELECT `id`, `type`, `pattern`, `placeholder`, `min`, `max`, `step` 
										FROM `iv_qq_params_input` WHERE `id_qq`=".$r['id']."");
				if(!$q){exit(json_encode(array("error"=>"iv_qq_params_input \r\n".$mysqli->error)));}
				$r['params'] = $q->fetch_assoc();
			
			break;			
			case 2: // из списка
				$q = $mysqli->query("SELECT `id`, `value` FROM `iv_qq_params_variants` WHERE `id_qq`=".$r['id']." ORDER BY order_index");
				if(!$q){exit(json_encode(array("error"=>"iv_qq_params_variants \r\n".$mysqli->error)));}
				while($r1=$q->fetch_assoc()){$r['params'][] = $r1;}
			
			break;			
			case 3: // справочник
				$q = $mysqli->query("SELECT iv_qq_params_dir.id, iv_qq_params_dir.id_dir,
									 iv_directories.`name`, iv_directories.`desc`, iv_directories.`table`
									 FROM `iv_qq_params_dir` 
										LEFT JOIN iv_directories ON(iv_directories.id = iv_qq_params_dir.id_dir)
									 WHERE  iv_qq_params_dir.id_qq =".$r['id']." LIMIT 1");
				if(!$q){exit(json_encode(array("error"=>"iv_qq_params_dir \r\n".$mysqli->error)));}
				$r['params'] = $q->fetch_assoc();


				$q = $mysqli->query("SELECT `name`, `value`, `is_equall` FROM `iv_directories_param` WHERE `id_dir`=".$r['params']['id_dir']." LIMIT 1");
				if(!$q){exit(json_encode(array("error"=>"iv_directories_param \r\n".$mysqli->error)));}
				$r0 = $q->fetch_assoc();
				$dir_fields = array();
				$q = $mysqli->query("SELECT id, name FROM `".$r['params']['table']."` WHERE `".$r0['name']."`='".$r0['value']."'");
				if($q && $q->num_rows>0){
					while($r1 = $q->fetch_assoc()){
						$dir_fields[] = $r1;
					}
				}
				
				$r['dir'] = $dir_fields;
				
			break;
			
		}
		
		$result[] = $r;
	}
	

	
	
	echo json_encode($result);

?>