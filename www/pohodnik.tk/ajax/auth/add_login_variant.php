<?php
    include '../../blocks/for_auth.php';
    include '../../blocks/global.php';
    include '../../blocks/db.php';

    $avatar = isset($_POST['avatar']) ? $_POST['avatar']: null;
    $birthday = isset($_POST['birthday']) && !empty($_POST['birthday']) ? date_parse_from_format("d.m.Y", $_POST['birthday']): null;
    $email = isset($_POST['email']) ? $_POST['email']: null;
    $name = isset($_POST['name']) ? $_POST['name']: null;
    $provider = isset($_POST['provider']) ? $_POST['provider']: null;
    $sex = isset($_POST['sex']) ? $_POST['sex']: null;
    $socialId = isset($_POST['socialId']) ? $_POST['socialId']: null;
    $socialPage = isset($_POST['socialPage']) ? $_POST['socialPage']: null;
    $updateEmail = isset($_POST['updateEmail']) ? $_POST['updateEmail']: null;
    $updateAva = isset($_POST['updateAva']) ? $_POST['updateAva']: null;
    $uid = intval($_COOKIE["user"]);

    $q = $mysqli->query("SELECT 
    surname, sex, dob
    FROM `users`
    WHERE id={$uid}
    LIMIT 1
");
$r = $q->fetch_assoc();

$patch = array();

if($updateEmail && !empty($email)){
    $patch['email'] = $email;
}

if($updateAva && !empty($avatar)){
    $patch['photo_50'] = $avatar;
    $patch['photo_50'] = $avatar;
    $patch['photo_100'] = $avatar;
    $patch['photo_200_orig'] = $avatar;
    $patch['photo_max'] = $avatar;
    $patch['photo_max_orig'] = $avatar;
}

if(intval($r['sex'])<1) {
    $patch['sex'] = $sex == 'male' ? 1 : 2;
}


if(empty($r['dob']) && !empty($birthday)) {
    $patch['dob'] = date('Y-m-d', $birthday);
}


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
    die(err($mysqli->error));
}

if(count($patch)>0){
    $z = "
    UPDATE `users` SET 
    ".implode(",", array_map(function($key, $val){
        return "`{$key}`='{$val}'";
    }, array_keys($patch), $patch))."
    WHERE id={$uid}
    ";
    $q = $mysqli->query($z);

    if(!$q) {
    die(err($mysqli->error."\n{$z}"));
    }
}

die(out(array("success" => true, "updated" => $patch)));


?>
