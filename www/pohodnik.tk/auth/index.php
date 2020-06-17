<?php
if(!isset($_SESSION) || empty($_SESSION)){
    session_start();
}
require_once '../ajax/lib/SocialAuther/autoload.php';
include('../ajax/auth/adapters_config.php');
include('../blocks/global.php');

if(!isset($_GET['provider'])) {
    die(err("Не определен провайдер"));
}

if(!array_key_exists($_GET['provider'],$adapterConfigs)) {
    die(err("Не найден провайдер {$_GET['provider']}"));
}

$success = false;
$new = false;
$idUser = null;


$class = 'SocialAuther\Adapter\\' . ucfirst($_GET['provider']);
$adapter = new $class($adapterConfigs[$_GET['provider']]);

$auther = new SocialAuther\SocialAuther($adapter);

    if ($auther->authenticate()) {

        include('../blocks/db.php');

        $q = $mysqli->query("
        SELECT id_user, email, social_id FROM user_login_variants 
        WHERE 
            (
                social_id='{$auther->getSocialId()}' OR 
                email='{$auther->getEmail()}' OR 
                login='{$auther->getSocialId()}'
            ) AND (
                network='{$auther->getProvider()}' OR
                provider='{$auther->getProvider()}'
            ) LIMIT 1");
        if($q && $q->num_rows===1){ // этот пользователь уже задодил под этой социалкой
            $res = $q->fetch_assoc();
            $id_user = $res["id_user"];
            $idUser = $id_user;
            $access_token = $auther->getAccessToken();

            if(
                $mysqli->query("
                INSERT INTO `user_hash`
                (`id_user`, `hash`, `date_start`) VALUES 
                ({$id_user},'{$access_token}',NOW())
            ")
            ){
                setcookie("hash", $access_token, time()+86400*7,"/");
                setcookie("user", $id_user,time()+86400*7,"/");
            }
            $success = true;
        } else {
            // такого пользователя мы еще не знаем

            $success = true;
            $new = true;
            
            setcookie("hash", $auther->getAccessToken(), time()+86400,"/");
        }
        

    } else {
		print_r($auther);
        echo('no auth');
        $success = false;
    }
    
    echo "<script>
    
    opener.postMessage(".json_encode(array(
        "result" => $success,
        "new" => $new,
        "uid" => $idUser,
        "data" => array(
            "provider"   => $auther->getProvider(),
            "socialId"   => $auther->getSocialId(),
            "name"       => $auther->getName(),
            "email"      => $auther->getEmail(),
            "socialPage" => $auther->getSocialPage(),
            "sex"        => $auther->getSex(),
            "birthday"   => $auther->getBirthday(),
            "avatar"     => $auther->getAvatar(),
            "token"      => $auther->getAccessToken()
        )
    )).", '*');
    
    </script>";


