<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
include("../../blocks/global.php"); //Только для авторизованных
$result = array();
$id = intval($_POST['id']);
$uid = intval($_COOKIE["user"]);

$z = "
INSERT INTO `routes`(`id_copySrc`, `name`, `desc`, `center_coordinates`, `zoom`, `length`, `id_author`, `id_type`, `controls`, `date_create`, `preview_img`) 
SELECT {$id} as id_copySrc, CONCAT(`name`, ' (копия)') as name, `desc`, `center_coordinates`, `zoom`, `length`, {$uid} as `id_author`, `id_type`, `controls`, NOW() AS `date_create`, `preview_img` FROM `routes` WHERE id={$id}";
$q = $mysqli -> query($z);

if(!$q) {
    die(err(array('error'=>$mysqli->error, 'q' => $z)));
}

$new_route_id = $mysqli->insert_id;

$z = "
INSERT INTO `route_objects`(`id_route`, `name`, `desc`, `coordinates`, `id_typeobject`, `icon_url`, `stroke_color`, `stroke_opacity`, `stroke_width`,`id_creator`, `date_create`, `id_editor`, `date_last_modif`,`distance`, `is_in_distance`, `icon_id`, `ord`, `is_confirm`)
SELECT {$new_route_id} as `id_route`, `name`, `desc`, `coordinates`, `id_typeobject`,`icon_url`, `stroke_color`, `stroke_opacity`, `stroke_width`,{$uid} as `id_creator`, NOW() as `date_create`, {$uid} `id_editor`, NOW() AS `date_last_modif`,`distance`, `is_in_distance`, `icon_id`, `ord`, `is_confirm` FROM `route_objects` WHERE id_route={$id}";
$q = $mysqli -> query($z);

if(!$q) {
    die(err(array('error'=>$mysqli->error,'type'=> 'route objects',  'q' => $z)));
}

$z = "INSERT INTO `route_regions`(`id_route`, `id_region`) SELECT  {$new_route_id} as `id_route`, `id_region` FROM `route_regions` WHERE id_route={$id}";
$q = $mysqli -> query($z);

if(!$q) {
    die(err(array('error'=>$mysqli->error,'type'=> 'route objects',  'q' => $z)));
}

die(out(array('success' => true, 'id' => $new_route_id)));