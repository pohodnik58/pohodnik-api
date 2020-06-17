<?php
    if(isset($_POST['user']) && isset($_POST['password'])) {
            include("../../blocks/db.php"); //подключение к БД

$login =		$mysqli->real_escape_string(trim(strtolower($_POST["user"])));
$pass  =		$mysqli->real_escape_string(trim($_POST["password"]));
$hash  =		uniqid("poh").rand(100,999).'x';

$q = $mysqli->query("SELECT id_user, password FROM user_login_variants WHERE login='{$login}' LIMIT 1");
if($q && $q->num_rows===1){
	$res = $q->fetch_assoc();
	$id_user = $res["id_user"];
	if($res["password"]===md5(md5($pass))){
		if($mysqli->query("INSERT INTO user_hash SET hash='{$hash}', date_start=NOW(), id_user={$id_user}")){

		    $timeout = $is_remember ? time() + (86400 * 7) : time() + 3600;
			setcookie("hash", $hash, $timeout, "/");
			setcookie("user", $res["id_user"], $timeout, "/");
            
            echo "<script>
    
            opener.postMessage(".json_encode(array(
                "result" => true,
                "new" => false,
                "uid" => $id_user,
                "data" => array(
                    "token" => $hash
                )
            )).", '*');
            
            </script>";



		} else {
		    echo("<p  style='color: red'>Ошибка авторизации</p>");
		}
	} else {
		echo("<p style='color: red'>Неправильный пароль</p>");
	};
}else{
	echo("<p  style='color: red'>Неизвестный пользователь</p>");
};
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <style>
        body {
            font-family: Roboto, Arial;
            padding: 40px;
        }

        input, button {
            padding: 8px 16px;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <form method="POST">
        <input type="text" placeholder="Логин" required name="user" value="<?= isset($_GET['login'])?$_GET['login']:'' ?>"><br>
        <input type="password" placeholder="Пароль" required name="password" <?= isset($_GET['login'])?"autofocus":'' ?>><br>
        <button type="submit">Войти</button>
    </form>
</body>
</html>