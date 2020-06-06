<?php
    require_once '../lib/SocialAuther/autoload.php';
    include './adapters_config.php';
    include '../../blocks/global.php';
    include '../../blocks/db.php';
    $adapters = array();

    $socialId = $_GET['socialId'];
    $email = $_GET['email'];



    if(isset($_COOKIE["hash"]) && isset($_GET['token']) && $_COOKIE["hash"] == $_GET['token']){
        $res = array();
        $q = $mysqli->query("
        SELECT
            ulv.id_user, ulv.provider, ulv.email, ulv.network,
            users.name, users.surname, users.photo_50
        FROM
            user_login_variants as ulv LEFT JOIN users ON users.id = ulv.id_user 
        WHERE
            ulv.email='{$email}'
        ");

        
        while($r = $q->fetch_assoc()){
            $res[] = $r;
        }

        $q = $mysqli->query("
        SELECT
            ulv.id_user, ulv.provider, ulv.email, ulv.network,
            users.name, users.surname, users.photo_50
        FROM
            user_login_variants as ulv LEFT JOIN users ON users.id = ulv.id_user 
        WHERE users.email='{$email}'
        ");

        while($r = $q->fetch_assoc()){
            $res[] = $r;
        }


        if(count($res)){ // этот пользователь уже задодил под этой социалкой

            foreach ($res as $variant) {
                # code...
                if(array_key_exists($variant['provider'],$adapterConfigs)){
                    $class = 'SocialAuther\Adapter\\' . ucfirst($variant['provider']);
                    $adap = new $class($adapterConfigs[$variant['provider']]);
                    $variant['url'] = $adap->getAuthUrl();
                    $variant['name'] = $adap->getFullName();
                }
            }
        }
    
        die(out($res));
    } else {
        die(err("Нет доступа"));
    }


?>
