<?php
	include('../../../blocks/db.php');
	include("../../../blocks/for_auth.php"); //Только для авторизованных
	$id_user = $_COOKIE["user"];
	$email = $mysqli->real_escape_string($_POST['email']);

$q = $mysqli->query("SELECT id FROM  user_subscribes WHERE email='{$email}'");
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
if($q->num_rows>0){die(json_encode(array("error"=>'email yже используется')));}


	$q = $mysqli->query("INSERT INTO user_subscribes SET email='{$email}', id_user={$id_user}, confirm_code='".md5(time()."sda")."'");
	if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
	$id = $mysqli->insert_id;
	if(isset($_POST['regions']) && is_array($_POST['regions']) && count($_POST['regions'])>0){
		$vls = array();
		foreach($_POST['regions'] as $x){$vls[] = "({$id},{$x})";}
		$q = $mysqli->query("INSERT INTO `user_subscribes_regions`(`id_subs`, `id_region`) VALUES ".implode(',', $vls));
	}
	if(isset($_POST['types']) && is_array($_POST['types']) && count($_POST['types'])>0){
		$vls = array();
		foreach($_POST['types'] as $x){$vls[] = "({$id},{$x})";}
		$q = $mysqli->query("INSERT INTO `user_subscribes_types`(`id_subs`, `id_type`) VALUES ".implode(',', $vls));
	}

	die(json_encode(array("success"=>true, "id"=>$id)));
