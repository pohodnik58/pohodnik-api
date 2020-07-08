<?php
include("../../../blocks/db.php");
include("../../../blocks/for_auth.php");
include("../../../blocks/err.php");
include("../../../blocks/global.php");
$result = array();
$id_user = $_COOKIE["user"];
$id_hiking = isset($_POST['id_hiking'])?intval($_POST['id_hiking']):0;
$id_vacancy = isset($_POST['id_vacancy'])?intval($_POST['id_vacancy']):0;
$comment = isset($_POST['comment'])?$mysqli->real_escape_string($_POST['comment']):'';


if(!($id_hiking>0)){die(err("id_hiking is undefined"));}
if(!($id_vacancy>0)){die(err("id_vacancy is undefined"));}

$q = $mysqli->query("SELECT id FROM `hiking_members` WHERE id_hiking={$id_hiking} AND id_user={$id_user} LIMIT 1");
if($q && $q->num_rows===0){ die(err("Только для участников похода {$id_hiking}"));}

$q = $mysqli->query("SELECT id FROM `hiking_vacancies_response` WHERE `id_hiking_vacancy`={$id_vacancy} AND `id_user`={$id_user} LIMIT 1");
if($q && $q->num_rows===1){ die(err("Вы уже откликнулись на эту вакансию."));}

$z = "INSERT INTO `hiking_vacancies_response` SET `id_hiking_vacancy`={$id_vacancy}, `id_user`={$id_user}, `date`=NOW(), `comment`='{$comment}'";
$q = $mysqli->query($z);
if(!$q) { die(err($mysqli->error, array("z" => $z)));}

die(out(array(
    "success" => true,
    "affected" => $mysqli->affected_rows,
    "id" => $mysqli->insert_id
)));