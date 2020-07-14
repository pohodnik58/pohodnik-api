<?php
include("../../../blocks/db.php");
include("../../../blocks/for_auth.php");
include("../../../blocks/err.php");
include("../../../blocks/global.php");

$id_user = $_COOKIE["user"];
$id_hiking = isset($_GET['id_hiking'])?intval($_GET['id_hiking']):0;

if(!($id_hiking>0)){die(err("id_hiking is undefined"));}

$z = "SELECT
        medicaments.*,
        hiking_first_aid_kit.*
      FROM
        hiking_first_aid_kit
        LEFT JOIN medicaments ON medicaments.id = hiking_first_aid_kit.id_medicament
      WHERE
        hiking_first_aid_kit.id_hiking={$id_hiking} ";
$q = $mysqli->query($z);
if(!$q) { die(err($mysqli->error, array("z" => $z)));}

$res = array();

while ($r = $q -> fetch_assoc()) {
    $res[] = $r;
}

die(out($res));
