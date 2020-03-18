<?php
include("../blocks/db.php"); //подключение к БД
if (isset($_COOKIE["user"]) && $_COOKIE["user"]>0){
	$q = $mysqli->query("SELECT `id`, `email`, `name`, `surname`, `sex`, UNIX_TIMESTAMP(`dob`) AS dob, UNIX_TIMESTAMP(`reg_date`) AS reg_date, `ava`, `admin`, `address`, `phone`, `skype`, `icq` FROM users WHERE id=".$_COOKIE["user"]." LIMIT 1");
	if($q && $q->num_rows===1){
		$r = $q->fetch_assoc();
		$r['dob'] = $r['dob']>0?date('d.m.Y',$r['dob']):' - Не указана - ';
		$r['sex'] = $r['sex']==0?'бесполое': ($r['sex']==1?'мужской':'женский') ;
		echo json_encode($r);
	}else{exit(json_encode(array("error"=>"Ошибка при получении данных пользователя. \r\n".$mysqli->error)));}
}else{
	exit(json_encode(array("error"=>"Пользователь не авторизован. \r\n")));
};
?>