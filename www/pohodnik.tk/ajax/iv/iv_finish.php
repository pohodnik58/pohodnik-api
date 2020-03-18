<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$data = $_POST['data'];
$id_user = $_COOKIE["user"];
$ans_id = array();


foreach($data  as $key => $value){$ans_id[] = $key;}

$q = $mysqli->query("SELECT id, `id_type`, `is_custom`, `is_require`, `is_multi` FROM `iv_qq` WHERE `id` IN(".implode(',',$ans_id).")");
if($q && $q->num_rows>0){
	while($r = $q->fetch_assoc()){
		if($r['is_multi']==0 && count($data[$r['id']])>1){
			exit(json_encode(array("error"=>"Эм... Кажется несолько ответов на вопрос давать нельзя...")));
		}			
		if($r['is_require']==0 && count($data[$r['id']])==0){
			exit(json_encode(array("error"=>"Эм... Кажется вы забыли ответить на какой-то вопрос...")));
		}

		if($mysqli->query("INSERT INTO `iv_ans` SET `id_qq`=".$r['id'].",`id_user`=".$id_user.",`date`='".date('Y-m-d H:i:s')."' ")){
			$id_ans = $mysqli->insert_id;
			for($i =0; $i<count($data[$r['id']]); $i++){
				$z = "INSERT INTO `iv_ans_content` SET
						`id_ans`={$id_ans},
						`v_from_input`='".($r['id_type']==1?$data[$r['id']][$i]['value']:"")."',
						`v_from_variants`=".(($r['id_type']==2 && $data[$r['id']][$i]['custom']!='true')?$data[$r['id']][$i]['value']:"0").",
						`v_from_dir`=".(($r['id_type']==3 && $data[$r['id']][$i]['custom']!='true')?$data[$r['id']][$i]['value']:"0").",
						`v_custom`='".($data[$r['id']][$i]['custom']=='true'?$data[$r['id']][$i]['value']:"")."'";
				if($mysqli->query($z)){
					$result["success"]=true;
				} else {
					exit(json_encode(array("error"=>"Не могу записать варинат ответа... \r\n".$mysqli->error)));
				}
				
			}
		} else { exit(json_encode(array("error"=>"Не могу сообщить о вашем ответе \r\n".$mysqli->error)));}


		
	}
	echo json_encode($result);
}else{exit(json_encode(array("error"=>"Нет ответов на вопросы... \r\n".$mysqli->error)));}

?>