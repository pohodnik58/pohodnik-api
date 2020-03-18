<?php
include("../../blocks/db.php"); //подключение к БД
//include("../../blocks/for_auth.php"); //Только для авторизованных
//include("../../blocks/dates.php"); //Только для авторизованных
$result = array();
function base64ToImage($imageData, $name){
    $data = 'data:image/png;base64,AAAFBfj42Pj4';
    list($type, $imageData) = explode(';', $imageData);
    list(,$extension) = explode('/',$type);
    list(,$imageData)      = explode(',', $imageData);
    $fileName = $name.'.'.$extension;
    $imageData = base64_decode($imageData);
    file_put_contents($fileName, $imageData);
	
	return $extension;
}
$cou = 1;
$q = $mysqli->query("SELECT `id`, `id_travel`, `order_item`, `note` FROM `blog_travel_notes` WHERE id_travel=4");
if(!$q){die(json_encode(array('error'=>$mysqli->error)));}
while($r = $q->fetch_assoc()){
	$body = $r['note'];
	$cou = 1;
	$result = preg_match_all("/<img[^>]*?src\s*=\s*[\"']?(data[^\"']*)/", $body, $matches, PREG_SET_ORDER);  
	foreach($matches as $m){
		$body = str_replace($m[1], "../../images/blog/t".$r['id_travel']."n".$r['id']."_".$cou.".".base64ToImage($m[1], "../../images/blog/t".$r['id_travel']."n".$r['id']."_".$cou), $body);
		$cou++;
		if($body!=$r['note']){
			$mysqli->query("UPDATE blog_travel_notes SET note='{$body}' WHERE id=".$r['id']);
		}
	}
}
