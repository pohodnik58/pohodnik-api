<?php
require_once '../lib/SocialAuther/autoload.php';

$adapterConfigs = array(
    'vk' => array(
        'client_id'     => '6499514',
        'client_secret' => 'thztiCXqcTYm2oaXZq4h',
        'redirect_uri'  => 'https://pohodnik.tk/auth/?provider=vk',
		'bg'=>'#6383A8',
		'icon'=>'icons/vk.svg',
		'name'=>'ВКонтакте'
		
    ),
    'odnoklassniki' => array(		'bg'=>'#F4731C',
		'icon'=>'icons/odnoklassniki.svg',
        'client_id'     => '1267313664',
        'client_secret' => '41FBFA3E60F32D40987922E2',
        'redirect_uri'  => 'https://pohodnik.tk/auth/?provider=odnoklassniki',
        'public_key'    => 'CBANKKIMEBABABABA',
		'name'=>'Одноклассники'

    ),
    'mailru' => array(
        'client_id'     => '760410',
        'client_secret' => '357aaf458d5acded3ec62dd04a0c5b94',
        'redirect_uri'  => 'https://pohodnik.tk/auth/?provider=mailru',
		'bg'=>'#168DE2',
		'icon'=>'icons/mail-dot-ru.svg',
		'name'=>'Mail.Ru'
    ),
    'yandex' => array(
        'client_id'     => '94f124fc09334032bba0acb78954db81',
        'client_secret' => 'fec8018a1528402099b92e1ce9b0e8cf',
        'redirect_uri'  => 'https://pohodnik.tk/auth/?provider=yandex',
		'bg'=>'#FF0000',
		'icon'=>'icons/yandex.svg',
		'name'=>'Яндекс'
    ),
    'google' => array(
        'client_id'     => '922332876703-smn70a4fbdr7teo3uihrvoab5smdbvl5.apps.googleusercontent.com',
        'client_secret' => '5LI8TK8hajc-ktIU_H8bnGcW',
        'redirect_uri'  => 'https://pohodnik.tk/auth/?provider=google',
		'bg'=>'#4285F4',
		'icon'=>'icons/google.svg',
		'name'=>'Google'
    ),
    'facebook' => array(
        'client_id'     => '2139245699623747',
        'client_secret' => '6d4c2b42b9f2255783fd45515f89541b',
        'redirect_uri'  => 'https://pohodnik.tk/auth/?provider=facebook',
		'bg'=>'#3B5998',
		'icon'=>'icons/facebook.svg',
		'name'=>'Facebook'
    ),
    'strava' => array(
        'client_id'     => '15626',
        'client_secret' => '45cc15025003629e265487c81ddafbeb1f668a59',
        'redirect_uri'  => 'https://pohodnik.tk/auth/?provider=strava',
		'bg'=>'#FC4C02',
		'icon'=>'icons/strava.svg',
		'name'=>'Strava'
    )
);


$adapters = array();
foreach ($adapterConfigs as $adapter => $settings) {
    $class = 'SocialAuther\Adapter\\' . ucfirst($adapter);
    $adapters[$adapter] = new $class($settings);
}

if (isset($_GET['provider']) && array_key_exists($_GET['provider'], $adapters) /*&& !isset($_SESSION['user'])*/) {
    $auther = new SocialAuther\SocialAuther($adapters[$_GET['provider']]);

    if ($auther->authenticate()) {


        if (!false) {
            $values = array(
                $auther->getProvider(),
                $auther->getSocialId(),
                $auther->getName(),
                $auther->getEmail(),
                $auther->getSocialPage(),
                $auther->getSex(),
                date('Y-m-d', strtotime($auther->getBirthday())),
                $auther->getAvatar()
            );
			
			echo implode('<li>',$values);
			
			print_r();

   
        } else {
            $userFromDb = new stdClass();
            $userFromDb->provider   = $record['provider'];
            $userFromDb->socialId   = $record['social_id'];
            $userFromDb->name       = $record['name'];
            $userFromDb->email      = $record['email'];
            $userFromDb->socialPage = $record['social_page'];
            $userFromDb->sex        = $record['sex'];
            $userFromDb->birthday   = date('m.d.Y', strtotime($record['birthday']));
            $userFromDb->avatar     = $record['avatar'];
        }

        $user = new stdClass();
        $user->provider   = $auther->getProvider();
        $user->socialId   = $auther->getSocialId();
        $user->name       = $auther->getName();
        $user->email      = $auther->getEmail();
        $user->socialPage = $auther->getSocialPage();
        $user->sex        = $auther->getSex();
        $user->birthday   = $auther->getBirthday();
        $user->avatar     = $auther->getAvatar();

        if (true) {
            $idToUpdate = $record['id'];
            $birthday = date('Y-m-d', strtotime($user->birthday));

            print_r(
                "UPDATE `users` SET " .
                "`social_id` = '{$user->socialId}', `name` = '{$user->name}', `email` = '{$user->email}', " .
                "`social_page` = '{$user->socialPage}', `sex` = '{$user->sex}', " .
                "`birthday` = '{$birthday}', `avatar` = '$user->avatar' " .
                "WHERE `id`='{$idToUpdate}'"
            );
        }

        //$_SESSION['user'] = $user;
    } else {
		print_r($auther);
		echo('no auth');
	}

    //header("location:index.php");
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <title></title>
	<style>svg { fill:#fff}
	p {  display:inline-block; margin:6px; }
	.social_box {
		display:inline-block; height:40px; padding:8px 16px;fill:#fff;
		line-height:40px!important;
		color:#fff;
		min-width:200px;
		font-family: Arial;
		font-size:18px;
	}
	
	
	.social_box img {
		margin-left:15px;
		float:right;
		  transform: translateX(-9999px);
			filter: drop-shadow(9999px 0 0 #fff);
	}	
	</style>
</head>
<body>

<?php
if (isset($_SESSION['user'])) {
    echo '<p><a href="info.php">Скрытый контент</a></p>';
} else if (!isset($_GET['code']) && !isset($_SESSION['user'])) {
    foreach ($adapters as $title => $adapter) {
        echo '<p><a href="' . $adapter->getAuthUrl() . '">' .  
				'<span class="social_box" style="background:'.$adapter->getBG() . '">'.
				$adapter->getFullName().'<img src="'.$adapter->getIcon().'" height=40></span></a></p>';
    }
}
?>

</body>
</html>