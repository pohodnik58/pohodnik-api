<?php
include("../../blocks/db.php");
include("../../blocks/for_auth.php");
include("../../blocks/err.php");
include("../../blocks/global.php");

$id_user = $_COOKIE["user"];
$id = isset($_POST['id'])?intval($_POST['id']):0;

if(!($id>0)){die(err("id is undefined"));}

$z = "DELETE FROM `medicaments` WHERE `id`={$id}";
$q = $mysqli->query($z);
if(!$q) { die(err($mysqli->error, array("z" => $z, "patch" => $patch)));}

die(out(array(
    "success" => true,
    "affected" => $mysqli->affected_rows
)));
