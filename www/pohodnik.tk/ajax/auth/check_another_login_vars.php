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
            ulv.id_user, ulv.provider, ulv.email, ulv.network, ulv.login,
            users.name, users.surname, users.photo_50
        FROM
            user_login_variants as ulv LEFT JOIN users ON users.id = ulv.id_user 
        WHERE 
            (LENGTH(users.email) > 0 AND users.email='{$email}') 
            OR (LENGTH(ulv.login) > 0 AND ulv.login='{$email}')
            OR ulv.social_id='{$socialId}'
        ");

        while($r = $q->fetch_assoc()){
            $res[] = $r;
        }


        if(count($res)){ // этот пользователь уже задодил под этой социалкой
            for($i=0; $i<count($res); $i++){
                $variant = $res[$i];
                if(array_key_exists($variant['provider'],$adapterConfigs)){
                    $class = 'SocialAuther\Adapter\\' . ucfirst($variant['provider']);
                    $adap = new $class($adapterConfigs[$variant['provider']]);
                    $res[$i]['url'] = $adap->getAuthUrl();
                    $res[$i]['provider_name'] = $adap->getFullName();
                }

                if($variant['network'] == 'login') {
                    $res[$i]['url'] = "/auth/legacy?login={$variant['login']}";
                    $res[$i]['provider_name'] = "Логин/Пароль";
                }
            }
        }
    
        die(out($res));
    } else {
        die(err("Нет доступа"));
    }


?>
