<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_user = $_COOKIE["user"];
$q = $mysqli->query("	SELECT `iv`.`id`, `iv`.`name`, `iv`.`desc`, `iv`.`id_author`, `iv`.`date_start`, `iv`.`date_finish`, `iv`.`id_hiking`,
								users.name as author_name, users.surname as author_surname
						FROM `iv` LEFT JOIN users ON(`iv`.id_author = users.id) ORDER BY `iv`.`date_start` DESC");
if($q){
	while($r = $q->fetch_assoc()){
		$result[] = $r;
	}
		echo json_encode($result);
}else{exit(json_encode(array("error"=>"Ошибка добавления опроса. \r\n".$mysqli->error)));}

?>