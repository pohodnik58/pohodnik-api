<?php // СПИСКИ
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$data = $_POST['data'];
if(is_array($data)){
	for($i=0; $i<count($data); $i++){
		$q = $mysqli->query("UPDATE iv_qq_params_variants SET order_index=".$data[$i]['index']." WHERE id=".$data[$i]['id']."");
		if($q){$result[] = array("success"=>array($data[$i]['id'],$data[$i]['index']));} else {
			$result[] = array("error"=>$mysqli->error);
		}
	}
}
echo json_encode($result);

?>