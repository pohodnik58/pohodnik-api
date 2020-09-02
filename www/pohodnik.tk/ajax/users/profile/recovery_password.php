<?php
    session_start();
    unset($_SESSION['password_recovery']);
    unset($_SESSION['password_recovery_uid']);

	include('../../../blocks/db.php');
    include('../../../blocks/global.php');
    
    $loginOrEmail = $mysqli->real_escape_string(trim($_POST['loginOrEmail']));
    $redirectTo = $mysqli->real_escape_string(trim($_POST['redirectTo']));
    
    $q = $mysqli->query("
    SELECT
        ulv.id_user, users.name, users.surname, users.email, users.sex
    FROM
        user_login_variants as ulv LEFT JOIN users ON users.id = ulv.id_user 
    WHERE 
        users.email='{$loginOrEmail}'
        OR ulv.login='{$loginOrEmail}'
        OR ulv.email='{$loginOrEmail}'
    LIMIT 1");

    if(!$q) {
        die(err($mysqli->error));
    }

    if($q->num_rows == 0) {
        die(err("Не найден пользователь с таким email или логином"));
    }

    $user = $q->fetch_assoc();

    $_SESSION['password_recovery'] = uniqid();
    $_SESSION['password_recovery_uid'] = $user['id_user'];
    
	$url = "https://pohodnik.tk/changePassword/" . $_SESSION['password_recovery'] . "?login=".$loginOrEmail."&return=".$redirectTo;
	$subject = "Смена пароля на сайте pohodnik.tk"; 
	$obr = $user['sex']==2?'дорогая':'дорогой';
	$message = " 
	<html> 
	    <head> 
	        <title>Смена пароля на сайте pohodnik.tk</title> 
	    </head> 
	    <body> 
	        <p>Здравствуйте, ".$user['name'].".</p>
	        <p>Для вашей учетной записи поступил запрос на восстановление пароля.</p>
	        <p>Если вы этого не делали. Просто проигнорируйте это письмо.</p>
            <p>
                Если вы дейстительно желаете вспомнить пароль, вам нужно перейти по ссылке для
                <a href=\"{$url}\">восстановления пароля</a>.
            </p>
	        <p>-- <br>
            Почтовый сервис сайта Походники.<br>
            письмо сгенерировано автоматически
            </p>
	    </body> 
	</html>"; 
	$to = $user['name']." ".$user['surname']." <".$user['email'].">";
    $headers = "From: Помощник по восстановлению паролей <info@pohodnik.tk>\r\n";
    $headers .= "Reply-To: info@pohodnik.tk\r\n";
    $headers .= "X-Mailer: PHP ".phpversion()."\r\n";
	$headers  .= "Content-type: text/html; charset=utf-8\r\n"; 

    
$mailResult = mail($to, $subject, $message, $headers);
if(!$mailResult) {
    die(err(error_get_last()['message'], array("message"=>$message)));
}

die(json_encode(array("success"=>true, "email" => substr_replace($user['email'], '×××', 2, 3) )));

?>