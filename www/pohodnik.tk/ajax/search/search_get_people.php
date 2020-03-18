<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result= array();
$id_user = $_COOKIE["user"];
$search_str = array();
$input_str = $mysqli->real_escape_string(trim($_POST['input_str']));
$search_str = explode(" ",$input_str);
if(count($search_str)===0){$search_str = array($input_str);}
$str = array();
for($i=0; $i<count($search_str); $i++){
	$str[] = "name like '%".$search_str[$i]."%'";
	$str[] = "surname like '%".$search_str[$i]."%'";
}
if(count($search_str)===0){$search_str = array($input_str);}
$q = $mysqli->query("SELECT distinct users.id, 
							users.name, 
							users.surname, 
							users.ava, 
							(SELECT count(*) from friends WHERE id_user = ".$id_user." and id_friend =users.id LIMIT 1) as is_friend, 
							(SELECT count(*) from friends_request WHERE id_from = ".$id_user." AND id_to =users.id AND confirm = 0 LIMIT 1) as is_out_req, 
							(SELECT count(*) from friends_request WHERE id_to = ".$id_user." AND id_from =users.id AND confirm = 0 LIMIT 1) as is_in_req
					 FROM users, friends, friends_request 
					 WHERE ". implode(" OR ", $str));
if(!$q){exit(json_encode(array("error"=>"Ошибка при поиске. \r\n")));}
while($r = $q->fetch_assoc()){
	$result[] = $r;		
}
exit(json_encode($result));
?>