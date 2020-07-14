<?php
include("../../blocks/db.php");
include("../../blocks/for_auth.php");
include("../../blocks/err.php");
include("../../blocks/global.php");

$id_user = $_COOKIE["user"];
$name = isset($_POST['name'])?$mysqli->real_escape_string($_POST['name']):'';
$medical_group = isset($_POST['medical_group'])?$mysqli->real_escape_string($_POST['medical_group']):"";
$form = isset($_POST['form'])?$mysqli->real_escape_string($_POST['form']):"";
$dosage = isset($_POST['dosage'])?$mysqli->real_escape_string($_POST['dosage']):"";
$for_use = isset($_POST['for_use'])?$mysqli->real_escape_string($_POST['for_use']):"";
$contraindications = isset($_POST['contraindications'])?$mysqli->real_escape_string($_POST['contraindications']):"";

if(strlen($name)<3){die(err("Название препарата не корректно либо не заполнено"));}
if(strlen($dosage)<3){die(err("Введите рекомендуемую дозировку препарата"));}
if(strlen($for_use)<5){die(err("Введите показания к применению"));}

$z = "INSERT INTO `medicaments`( `name`, `medical_group`, `form`, `dosage`, `for_use`, `contraindications`) VALUES ('{$name}','{$medical_group}','{$form}','{$dosage}','{$for_use}','{$contraindications}')";
$q = $mysqli->query($z);
if(!$q) { die(err($mysqli->error, array("z" => $z)));}

die(out(array(
    "success" => true,
    "affected" => $mysqli->affected_rows,
    "id" => $mysqli->insert_id
)));
