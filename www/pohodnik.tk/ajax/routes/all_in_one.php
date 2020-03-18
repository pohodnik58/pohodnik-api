<?php
include("../../blocks/db.php"); //подключение к БД
//include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$claus = "";

if(isset($_GET['filter'])){
	if($_GET['filter']=='lines'){
		$claus = " WHERE id_typeobject=2";
	}
}


	$q = $mysqli->query("SELECT * FROM `route_objects` {$claus}");
	if($q){
		while($r=$q->fetch_assoc()){

						
			$result[] = $r;
		}
		echo(json_encode($result));
	}else{exit(json_encode(array("error"=>"Ошибка получения списка. \r\n".$mysqli->error)));}

?>