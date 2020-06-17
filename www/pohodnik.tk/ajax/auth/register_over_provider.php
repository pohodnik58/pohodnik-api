<?php
include '../../blocks/global.php';
include '../../blocks/db.php';

$avatar = isset($_POST['avatar']) ? $mysqli->real_escape_string($_POST['avatar']): "";
$birthday = isset($_POST['birthday']) && !empty($_POST['birthday']) ? strtotime($_POST['birthday']): null;
$email = isset($_POST['email']) ? $mysqli->real_escape_string($_POST['email']): null;
$name = isset($_POST['name']) ? $mysqli->real_escape_string($_POST['name']): null;
$provider = isset($_POST['provider']) ? $_POST['provider']: null;
$sex = isset($_POST['sex']) ? ($_POST['sex'] == 'male' ? 1 : 2) : 0;
$socialId = isset($_POST['socialId']) ? $_POST['socialId']: null;
$socialPage = isset($_POST['socialPage']) ? $_POST['socialPage']: null;
$token = isset($_POST['token']) ? $mysqli->real_escape_string($_POST['token']) : null;

if(empty($token)){
    die(err("Wrong token"));
}

if(empty($email)){
    die(err("Wrong email"));
}

$name_parts = explode(" ", $name);
$q = $mysqli->query("INSERT INTO `users` SET 
`email`='{$email}',
`name`='{$name_parts[0]}',
`surname`='{$name_parts[1]}',
`sex`='{$sex}',
`dob`=".(!empty($birthday)?"'".date('Y-m-d', $birthday)."'":"NULL").",
`reg_date`='".date('Y-m-d H:i:s')."',
`ava`='{$avatar}',
`photo_50`='{$avatar}',
`photo_100`='{$avatar}',
`photo_200_orig`='{$avatar}',
`photo_max`='{$avatar}',
`photo_max_orig`='{$avatar}'
");

if(!$q){
    die(err(array(
        "message"=>"Ошибка добавления пользователя",
        "error" => $mysqli->error
    )));
}

$uid = $mysqli->insert_id;

$q = $mysqli->query("
    INSERT INTO `user_login_variants` SET 
    `id_user`={$uid},
    `social_id`='{$socialId}',
    `email`='{$email}',
    `provider`='{$provider}',
    `network`='{$provider}',
    `social_page`=".(!empty($socialPage) && strtolower($socialPage)!='null'?"'{$socialPage}'":'NULL')."
");

if(!$q) {
    die(err(array(
        "message"=>"Ошибка добавления варианта входа",
        "error" => $mysqli->error
    )));
}


$q = $mysqli->query("INSERT INTO `user_hash`(`id_user`, `hash`, `date_start`) VALUES ({$uid},'{$token}',NOW())");
if(!$q){
    die(err(array(
        "message"=>"Ошибка добавления токена",
        "error" => $mysqli->error
    )));
}

setcookie("hash", $token,time()+86400*7,"/");
setcookie("user", $uid,time()+86400*7,"/");

echo(out(array("user"=>$uid, "token"=>$token)));