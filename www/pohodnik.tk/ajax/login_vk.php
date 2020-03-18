<?php
include("../blocks/db.php"); //подключение к БД
$mid=		intval($_POST["mid"]);
$sid=		$mysqli->real_escape_string(trim($_POST["sid"]));
$data = 	$_POST['data'];

$q = $mysqli->query("SELECT id_user FROM user_login_variants WHERE login='{$mid}' LIMIT 1");
if($q && $q->num_rows===1){
	$res = $q->fetch_assoc();
	$id_user = $res["id_user"];

		if($mysqli->query("INSERT INTO `user_hash`(`id_user`, `hash`, `date_start`) VALUES ({$id_user},'{$sid}',NOW())")){
			setcookie("hash", $sid,time()+86400*7,"/");
			setcookie("user", $id_user,time()+86400*7,"/");
			$newphoto = false;
			$q = $mysqli->query("SELECT photo_100 FROM users  WHERE id={$id_user} LIMIT 1");
			if($q && $q->num_rows===1){
			 $r = $q->fetch_assoc();
				if( substr($r['photo_100'], 0, 6) == '../../'){
					$newphoto = array(
						"photo_50"=>$data['photo_50'],
						"photo_100"=>$data['photo_100'],
						"photo_200_orig"=>$data['photo_200_orig'],
						"photo_max"=>$data['photo_max'],
						"photo_max_orig"=>$data['photo_max_orig']
					);
					
					$mysqli->query("UPDATE users SET 
						`ava` = '".$data['photo_50']."',
						`photo_50` = '".$data['photo_50']."',
						`photo_100` = '".$data['photo_100']."',
						`photo_200_orig` = '".$data['photo_200_orig']."',
						`photo_max` = '".$data['photo_max']."',
						`photo_max_orig` = '".$data['photo_max_orig']."'
					WHERE id=".$id_user);
					
				}
			} else {
				$newphoto = $mysqli->error;
			}
			
			echo(json_encode(array("user"=>$id_user,"photo"=>$newphoto)));
			
			
			
		}else{echo(json_encode(array("error"=>"Ошибка авторизации".$mysqli->error)));}

}else{
	if( $mysqli->query("INSERT INTO `users` SET 
						`email`='',
						`name`='".$data['first_name']."',
						`surname`='".$data['last_name']."',
						`sex`='".($data['sex']==1?2:1)."',
						`dob`='".date('Y-m-d H:i:s', strtotime($data['bdate']))."',
						`reg_date`='".date('Y-m-d H:i:s')."',
						`ava`='".$data['photo_50']."',
						`admin`=0,
						`photo_50`='".$data['photo_50']."',
						`photo_100`='".$data['photo_100']."',
						`photo_200_orig`='".$data['photo_200_orig']."',
						`photo_max`='".$data['photo_max']."',
						`photo_max_orig`='".$data['photo_max_orig']."'
				   ")){
	
			$id_user = $mysqli->insert_id;
		
		
		
		
			$q = $mysqli->query("INSERT INTO user_login_variants SET login='{$mid}', id_user={$id_user}, network='vk'");
			if(!$q){die(json_encode(array("error"=>"Ошибка добавления варианта залогинивания".$mysqli->error)));}
			
			$q = $mysqli->query("INSERT INTO `user_hash`(`id_user`, `hash`, `date_start`) VALUES ({$id_user},'{$sid}',NOW())");
			if(!$q){die(json_encode(array("error"=>"Ошибка добавления токена ".$mysqli->error)));}
			
			setcookie("hash", $sid,time()+86400*7,"/");
			setcookie("user", $id_user,time()+86400*7,"/");
			echo(json_encode(array("user"=>$id_user, "new"=>true)));	
	} else {
		die(json_encode(array("error"=>"Ошибка INSERT ".$mysqli->error)));
	}
	
};
?>