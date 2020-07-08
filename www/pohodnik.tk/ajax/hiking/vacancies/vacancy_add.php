<?php
include("../../../blocks/db.php");
include("../../../blocks/for_auth.php");
include("../../../blocks/err.php");
include("../../../blocks/global.php");
$result = array();
$id_user = $_COOKIE["user"];
$id_hiking = isset($_POST['id_hiking'])?intval($_POST['id_hiking']):0;
$id_position = isset($_POST['id_position'])?intval($_POST['id_position']):0;
$comment = isset($_POST['comment'])?$mysqli->real_escape_string($_POST['comment']):'';
$deadline = isset($_POST['deadline'])?$mysqli->real_escape_string($_POST['deadline']):"";

if(!($id_hiking>0)){die(err("id_hiking is undefined"));}
if(!($id_position>0)){die(err("id_position is undefined"));}
if(strlen($deadline)<10){die(err("deadline is incorrect"));}

$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_user} LIMIT 1");
if($q && $q->num_rows===0){ die(err("Нет доступа"));}

$z = "INSERT INTO `hiking_vacancies`(`id_hiking`, `id_position`, `comment`, `deadline`) VALUES ({$id_hiking},{$id_position},'{$comment}','{$deadline}')";
$q = $mysqli->query($z);
if(!$q) { die(err($mysqli->error, array("z" => $z)));}

die(out(array(
    "success" => true,
    "affected" => $mysqli->affected_rows,
    "id" => $mysqli->insert_id
)));