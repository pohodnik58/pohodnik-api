<?php // СПИСКИ
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_user = $_COOKIE["user"];
$id_qq = intval($_POST['id_qq']);
$name = $_POST['name'];
$exi_names = array();
$q = $mysqli->query("SELECT value FROM iv_qq_params_variants WHERE id_qq={$id_qq}");
if($q && $q->num_rows>0){
		while($r=$q->fetch_assoc()){
			$exi_names[] = $r['value'];
		}
}
if(!is_array($name)){$name = array($name);}
for($i=0;$i<count($name); $i++){

	$val = $mysqli->real_escape_string(trim($name[$i]));
	if(!in_array($val,$exi_names ) && strlen($val)>0){
		$q = $mysqli->query("INSERT INTO iv_qq_params_variants SET value='".$val."', id_qq={$id_qq}, order_index={$i}");
		if($q){$result[] = array("id"=>$mysqli->insert_id, "name"=>$val);}
	}
}
	echo json_encode($result);

?>