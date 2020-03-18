<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_user = $_COOKIE["user"];

$content = file_get_contents($_FILES['file']['tmp_name']);
$data = json_decode($content, true);


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


if(isset($data) && isset($data['travels'])){

		$travels = array();
		$notes = array();
		$res = array();
		foreach($data['travels'] as $travel){
		
			$q = $mysqli->query("SELECT id, id_user, date FROM  blog_travels WHERE  date='".date('Y-m-d H:i:s', $travel['date'])."' AND id_user={$id_user} LIMIT 1");
			if($q && $q->num_rows===1){
				$r = $q->fetch_assoc();
				$id_travel = $r['id'];
			} else {
				$q = $mysqli->query("INSERT INTO `blog_travels` SET `id_user`={$id_user},
									`name`='".$mysqli->real_escape_string($travel['name'])."',
									`description`='".$mysqli->real_escape_string($travel['description'])."',`date`='".date('Y-m-d H:i:s', $travel['date'])."'");
				if($q){
					$id_travel = $mysqli->insert_id;
				}
			}
			
			if($id_travel>0){
				$travels[$travel['id']] =  $id_travel;
			}
			
			
			//$res[] = $travel;
		}
		
		
		
		foreach($data['notes'] as $note){
		
			if($travels[$note['id_travel']]>0){
		
				$q = $mysqli->query("SELECT id, id_travel, date FROM  blog_travel_notes WHERE  date='".date('Y-m-d H:i:s', $note['date'])."' AND id_travel=".$travels[$note['id_travel']]." LIMIT 1");
				if(!$q){ $notes[] = $mysqli->error; }
				if($q && $q->num_rows===1){
					$r = $q->fetch_assoc();
					$id_note = $r['id'];
				} else {
				

					$body = $note['note'];
					$cou = 1;
					$result = preg_match_all("/<img[^>]*?src\s*=\s*[\"']?(data[^\"']*)/", $body, $matches, PREG_SET_ORDER);  
					foreach($matches as $m){
						$body = str_replace($m[1], "/images/blog/t".$r['id_travel']."n".$r['id']."_".$cou.".".base64ToImage($m[1], "../../images/blog/t".$r['id_travel']."n".$r['id']."_".$cou), $body);
						$cou++;
					}

					$q = $mysqli->query("INSERT INTO `blog_travel_notes` SET 
						`id_travel`=".$travels[$note['id_travel']].",   
						`date` ='".date('Y-m-d H:i:s', $note['date'])."' ,
						`order_item`=".$note['order_item'].",
						`note`='".$mysqli->real_escape_string($body)."',
						`lat`='".$mysqli->real_escape_string($note['lat'])."',
						`lon`='".$mysqli->real_escape_string($note['lon'])."'
					");
					if($q){
						$id_note = $mysqli->insert_id;
					} else {
						$notes[] = $mysqli->error;
					}
				}
				
				if($id_note>0){
					$notes[$note['id']] =  $id_note;
				}
			
			} else {
				$notes[] = "bad id_travel";
			}
			
		}
		
		
		
		
		
		
		die(json_encode(array(
			"notes"=>$notes,
			"travels"=>$travels
		)));
	

/*$q = $mysqli->query("SELECT id FROM  hiking_editors WHERE  id_hiking={$id_hiking} AND id_user={$id_editor}");
if($q && $q->num_rows>0){
	die(json_encode(array("error"=>"Уже добавлен.".$mysqli->error)));
}

	$q = $mysqli->query("SELECT id FROM hiking WHERE id_author={$id_user} AND id={$id_hiking} LIMIT 1");
	if($q && $q->num_rows === 1){
		
		if($mysqli->query("INSERT INTO hiking_editors SET id_hiking={$id_hiking}, id_user={$id_editor}")){
			die(json_encode(array("success"=>true, "id"=>$mysqli->insert_id)));
		} else {
			die(json_encode(array("error"=>"Ошибка добавления.".$mysqli->error)));
		}
		
	} else {
		die(json_encode(array("error"=>"Доступ только у создателя похода.".$mysqli->error)));
	}

*/
} else {
	die(json_encode(array("error"=>"Не могу расшифровать")));
}



