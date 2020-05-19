<?php
require_once '../ajax/lib/SocialAuther/autoload.php';
include('../ajax/auth/adapters_config.php');
include('../blocks/global.php');

if(!isset($_GET['provider'])) {
    die(err("Не определен провайдер"));
}

if(!array_key_exists($_GET['provider'],$adapterConfigs)) {
    die(err("Не найден провайдер {$_GET['provider']}"));
}


if (isset($_GET['provider']) && array_key_exists($_GET['provider'], $adapterConfigs) /*&& !isset($_SESSION['user'])*/) {

    $class = 'SocialAuther\Adapter\\' . ucfirst($_GET['provider']);
    $adapter = new $class($adapterConfigs[$_GET['provider']]);

    $auther = new SocialAuther\SocialAuther($adapter);

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
} else {
die(err('что-то пошло не так'));
}
?>
