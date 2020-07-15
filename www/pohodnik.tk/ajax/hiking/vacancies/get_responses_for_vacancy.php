<?php
include("../../../blocks/db.php");
include("../../../blocks/for_auth.php");
include("../../../blocks/err.php");
include("../../../blocks/global.php");
$result = array();
$id_user = $_COOKIE["user"];
$id_hiking = isset($_GET['id_hiking'])?intval($_GET['id_hiking']):0;
$id_vacancy = isset($_GET['id_vacancy'])?intval($_GET['id_vacancy']):0;

if(!($id_hiking>0)){die(err("id_hiking is undefined"));}
if(!($id_vacancy>0)){die(err("id_vacancy is undefined"));}

$q = $mysqli->query("SELECT id FROM hiking WHERE id={$id_hiking}  AND id_author = {$id_user} LIMIT 1");
if($q && $q->num_rows===0){ die(err("Нет доступа"));}


$z = "SELECT
hiking_vacancies_response.*,
CONCAT(users.name,' ', users.surname) as user
FROM `hiking_vacancies_response`
LEFT JOIN users ON users.id = hiking_vacancies_response.id_user
WHERE hiking_vacancies_response.id_hiking_vacancy={$id_vacancy}";
$q = $mysqli->query($z);
if(!$q) { die(err($mysqli->error, array("z" => $z)));}
while ($r = $q -> fetch_assoc()) {
    $result[] = $r;
}
die(out($result));