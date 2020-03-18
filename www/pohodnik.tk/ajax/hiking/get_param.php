<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$name = $mysqli->real_escape_string(trim($_GET['name']));
$id_user = $_COOKIE["user"];
$id = isset($_GET['id'])?intval($_GET['id']):0;
if(!($id>0)){die(json_encode(array("error"=>"ID is undefined")));}




$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id}  AND id_author = {$id_user} LIMIT 1");
if($q && $q->num_rows===0){
	$q = $mysqli->query("SELECT id FROM hiking_editors WHERE id_hiking={$id} AND id_user = {$id_user} LIMIT 1");
	if($q && $q->num_rows===0){
		die(json_encode(array("error"=>"Нет доступа")));
	}
}

$avai = explode(',','id_type,id_route,name,desc,text,start,finish,color,bg,id_region,ava,is_vacant_route');
if(strripos($name,',')>0){$name=explode(',',$name);}
if(!is_array($name)){
	if(!in_array($name, $avai)){die(json_encode(array("error"=>"Недопустимый параметр {$name}")));}
} else {
	for($i=0; $i<count($name); $i++){
		if(!in_array($name[$i], $avai)){die(json_encode(array("error"=>"Недопустимый параметр ".$name[$i]."")));}
	}
}


$field = !is_array($name)?$name:implode('`,`', $name);
$q = $mysqli->query("SELECT `{$field}` FROM `hiking`  WHERE id={$id} LIMIT 1");
if($q && $q->num_rows===1){
	exit(json_encode($q->fetch_assoc()));
}else{exit(json_encode(array("error"=>"Ошибка обновления . \r\n".$mysqli->error)));}

?>