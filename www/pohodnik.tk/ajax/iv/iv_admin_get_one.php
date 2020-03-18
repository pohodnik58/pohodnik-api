<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id = intval($_GET['id']);
$id_user = $_COOKIE["user"];
$q = $mysqli->query("SELECT id as id_iv, name, `desc`, `id_author`, `date_start`, `date_finish`, `hello_text`, `by_text`, `members_limit`, id_hiking, main FROM `iv` WHERE id={$id} AND `id_author`={$id_user} LIMIT 1");
if($q){
	
	$result['iv'] = $q->fetch_assoc();
	$result['iv']['d1'] = date('Y-m-d',strtotime($result['iv']['date_start']));
	$result['iv']['d2'] = date('Y-m-d',strtotime($result['iv']['date_finish']));
	
	$qq = array();
	$qb = $mysqli->query("SELECT id, name, `text`, `id_type`, `is_custom`, `is_require`, `is_multi` , `is_private`
							FROM `iv_qq` WHERE `id_iv`={$id} ORDER BY order_index, id");
	if(!$qb){exit(json_encode(array("error"=>"iv_qq \r\n".$mysqli->error)));}
	while($r=$qb->fetch_assoc()){
		$qq_iv = $r;
		switch($qq_iv['id_type']){
			case 1: // Ввод
				$q = $mysqli->query("	SELECT `id`, `type`, `pattern`, `placeholder`, `min`, `max`, `step` 
										FROM `iv_qq_params_input` WHERE `id_qq`=".$qq_iv['id']."");
				if(!$q){exit(json_encode(array("error"=>"iv_qq_params_input \r\n".$mysqli->error)));}
				$qq_iv['params'] = $q->fetch_assoc();
			
			break;			
			case 2: // из списка
				$q = $mysqli->query("SELECT `id`, `value` FROM `iv_qq_params_variants` WHERE `id_qq`=".$qq_iv['id']." ORDER BY order_index");
				if(!$q){exit(json_encode(array("error"=>"iv_qq_params_variants \r\n".$mysqli->error)));}
				while($r1=$q->fetch_assoc()){$qq_iv['params'][] = $r1;}
			
			break;			
			case 3: // справочник
				$q = $mysqli->query("SELECT iv_qq_params_dir.id, iv_qq_params_dir.id_dir,
									 iv_directories.`name`, iv_directories.`desc`, iv_directories.`table`
									 FROM `iv_qq_params_dir` 
										LEFT JOIN iv_directories ON(iv_directories.id = iv_qq_params_dir.id_dir)
									 WHERE  iv_qq_params_dir.id_qq =".$qq_iv['id']." LIMIT 1");
				if(!$q){exit(json_encode(array("error"=>"iv_qq_params_dir \r\n".$mysqli->error)));}
				$qq_iv['params'] = $q->fetch_assoc();

				

				$z = "SELECT `name`, `value`, `is_equall` FROM `iv_directories_param` WHERE `id_dir`=".$qq_iv['params']['id_dir']." LIMIT 1";
				$q = $mysqli->query($z);
				if(!$q){exit(json_encode(array("type"=>"iv_directories_param", "error"=>$mysqli->error, "q"=>$z)));}
				$r0 = $q->fetch_assoc();
				$dir_fields = array();
				$q = $mysqli->query("SELECT id, name FROM `".$qq_iv['params']['table']."` WHERE `".$r0['name']."`='".$r0['value']."'");
				if($q && $q->num_rows>0){
					while($r1 = $q->fetch_assoc()){
						$dir_fields[] = $r1;
					}
				}
				
				$qq_iv['dir'] = $dir_fields;
				
			break;
			
		}
		
		$qq[] = $qq_iv;
	}
	
	$result['qq'] = $qq;
	
	
	
	echo json_encode($result);
}else{exit(json_encode(array("error"=>"Нет доступного вам опроса с таким айдишником \r\n".$mysqli->error)));}

?>