<?php
include("../../../blocks/db.php");
include("../../../blocks/for_auth.php");
include("../../../blocks/err.php");
include("../../../blocks/global.php");
$result = array();
$id_user = $_COOKIE["user"];
$id_hiking = isset($_GET['id_hiking'])?intval($_GET['id_hiking']):0;

if(!($id_hiking>0)){die(err("id_hiking is undefined"));}

$z = "SELECT
        positions.name,
        positions.description,
        hiking_vacancies.id AS id_vacancy,
        hiking_vacancies.*,
        COUNT(hiking_vacancies_response.id) AS responses
      FROM
        hiking_vacancies
        LEFT JOIN positions ON positions.id = hiking_vacancies.id_position
        LEFT JOIN hiking_vacancies_response ON hiking_vacancies_response.id_hiking_vacancy = hiking_vacancies.id
      WHERE
        hiking_vacancies.id_hiking={$id_hiking} ".(!isset($_GET['all']) ? " AND hiking_vacancies.is_active=1 AND hiking_vacancies.deadline>=NOW()" : "")."
        GROUP BY hiking_vacancies.id
        ";
$q = $mysqli->query($z);
if(!$q) { die(err($mysqli->error, array("z" => $z)));}

$res = array();

while ($r = $q -> fetch_assoc()) {
    $res[] = $r;
}

die(out($res));