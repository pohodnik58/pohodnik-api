<?php
include("../../../blocks/db.php");
include("../../../blocks/for_auth.php");
include("../../../blocks/err.php");
include("../../../blocks/global.php");
$result = array();
$id_user = $_COOKIE["user"];
$id_hiking = isset($_POST['id_hiking'])?intval($_POST['id_hiking']):0;
$id_hiking_vacancy = isset($_POST['id_hiking_vacancy'])?intval($_POST['id_hiking_vacancy']):0;

if(!($id_hiking>0)){die(err("id_hiking is undefined"));}
if(!($id_hiking_vacancy>0)){die(err("id_hiking_vacancy is undefined"));}

$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_user} LIMIT 1");
if($q && $q->num_rows===0){ die(err("Нет доступа"));}

$z = "UPDATE `hiking_vacancies_response` SET `approve_user_id`={$id_user},`approve_date`=NOW() WHERE id={$id_hiking_vacancy}";
$q = $mysqli->query($z);
if(!$q) { die(err($mysqli->error, array("z" => $z)));}


$z = "INSERT INTO `hiking_members_positions`(`id_hiking`, `id_user`, `id_position`, `id_author`, `date`, `comment`) SELECT";
$q = $mysqli->query($z);
if(!$q) { die(err($mysqli->error, array("z" => $z)));}

die(out(array(
    "success" => true,
    "affected" => $mysqli->affected_rows,
    "id" => $mysqli->insert_id
)));