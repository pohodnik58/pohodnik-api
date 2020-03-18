<?
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных



function saveAParam($name, $value, $mysqli){
	$id_user = $_COOKIE["user"];
	if(!in_array($name,explode(',','email,name,surname,dob,ava,address,phone,skype,icq,weight,growth,id_region,photo_50,photo_100,photo_200_orig,photo_max,photo_max_orig,uniq_code'))){
		return((array("error"=>"no in white list")));}
	if(in_array($name, explode(',','ava,photo_50,photo_100,photo_200_orig,photo_max,photo_max_orig'))){
		$q = $mysqli->query("SELECT `{$name}` FROM users WHERE id={$id_user} LIMIT 1");
		if($q && $q->num_rows===1){
			$r = $q->fetch_row();
			$filename = $r[0];
			if(is_file($filename)){
				unlink($filename);
			} else if(is_file('../../'.$filename)){
				unlink('../../'.$filename);
			}
		}
	}
	$q = $mysqli->query("UPDATE users SET `{$name}`='{$value}' WHERE id={$id_user}");
	if(!$q){return((array("error"=>$mysqli->error)));}
	return ((array("success"=>true)));
}


$name = is_array($_POST['name'])?$_POST['name']:$mysqli->real_escape_string(trim($_POST['name']));
$value = $mysqli->real_escape_string(trim($_POST['value']));

if(is_array($name)){
	for($i=0; $i<count($name); $i++){
		$res = saveAParam($name[$i], $value, $mysqli);
	}
	die(json_encode($res));
} else {
	die(json_encode(saveAParam($name, $value, $mysqli)));
}

//`