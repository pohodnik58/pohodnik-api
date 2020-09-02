<?php
    session_start();
	include('../../../blocks/db.php');
    include('../../../blocks/global.php');
    $psw1 = $mysqli->real_escape_string(trim($_POST['psw1']));
    $psw2 = $mysqli->real_escape_string(trim($_POST['psw2']));
    $code = $mysqli->real_escape_string(trim($_POST['code']));

    if(!empty($code) && empty($_SESSION['password_recovery'])) {
        die(err("Некорректный код"));
    }

    if(empty($code)) {
        include("../../../blocks/for_auth.php"); //Только для авторизованных
        $id_user = $_COOKIE["user"];
    } if(!empty($_SESSION['password_recovery_uid']) && $code == $_SESSION['password_recovery'] ){
        $id_user = $_SESSION['password_recovery_uid'];
        unset($_SESSION['password_recovery']);
        unset($_SESSION['password_recovery_uid']);
    } else {
        die(err("Ошибка авторизации", $_SESSION));
    }
    
    $z = "UPDATE user_login_variants SET password='".md5(md5($psw1))."' WHERE id_user={$id_user} AND LENGTH(password)>0";
    $q = $mysqli->query($z);

    if(!$q) {
        die(err($mysqli->error));
    }

    die(json_encode(array("success"=>true, "affected" => $mysqli->affected_rows )));

?>