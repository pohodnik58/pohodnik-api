<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_route = intval($_GET['id_route']);
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	function ErrorCatcher($errno, $errstr, $errfile, $errline){
		die(json_encode(array("error"=>$errstr."\n(".$errfile.":".$errline.")", "errno"=>$errno, "file"=>$errfile, "line"=>$errline )));
		exit();
		return false;
	}
	set_error_handler("ErrorCatcher");

if($id_route>0 ){

	$q = $mysqli->query("SELECT `name`, `desc`, UNIX_TIMESTAMP(`date_create`) AS date_create, id_author FROM routes WHERE id={$id_route} LIMIT 1");
	if($q && $q->num_rows===1){
		$r = $q->fetch_assoc();
		$name = $r['name'];
		$desc = $r['desc'];
		$date = $r['date_create'];
		if($r['id_author']>0){
			$q = $mysqli->query("SELECT CONCAT(surname, ' ', name) AS name, email, id FROM users WHERE id=".$r['id_author']." LIMIT 1");
			if(!$q){die($mysqli->error);}
			$author = $q->fetch_assoc();
		}
	} else {
		die(json_encode(array('error'=>$mysqli->error)));
	}


			$q1=$mysqli->query("SELECT	ro.id, ro.id_route,ro.name,ro.`desc`,ro.coordinates,
										ro.`id_typeobject`,ro.`stroke_color`,ro.`stroke_opacity`,ro.`stroke_width`,ro.`distance`,ro.`id_creator`,ro.`date_create`,ro.`id_editor`,
										UNIX_TIMESTAMP(ro.`date_last_modif`) AS date_last_modif
								FROM `route_objects` AS ro
								WHERE ro.id_route=".$id_route." ORDER BY ro.ord, ro.date_create");
			if(!$q1){ die(json_encode(array("error"=>$mysqli->error)));}
			while($r1=$q1->fetch_assoc()){
				$r1['coordinates'] = json_decode($r1['coordinates']);
				$result[]= $r1;	
			}
						
			
	
		
		$dom = new domDocument("1.0", "utf-8");
			$gpx = $dom->createElement("gpx");
			$gpx->setAttribute("xmlns", "http://www.topografix.com/GPX/1/1");
			$gpx->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
			$gpx->setAttribute("xsi:schemaLocation", "http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd");
			$gpx->setAttribute("creator", "http://pohodnik58.ru");
			
			$meta = $dom->createElement("metadata");
			$meta->appendChild($dom->createElement("name", $name));
			$meta->appendChild($dom->createElement("desc", $desc));
			if(isset($author)){
				$au = $dom->createElement("author");
				$au->appendChild($dom->createElement("name", $author['name']));
				if(strlen($author['email'])>0){$au->appendChild($dom->createElement("email", $author['email'])); }
				$meta->appendChild($au);
			}
			$meta->appendChild($dom->createElement("time", date('c',$date)));
			$gpx->appendChild($meta);
			


	
			foreach($result as $go ){
		
				$obj = $dom->createElement( $go['id_typeobject']==2?"trk":"wpt");
				if($go['id_typeobject']==1){ // если точка
					$obj->setAttribute("lat", $go['coordinates'][0]);
					$obj->setAttribute("lon", $go['coordinates'][1]);
					if(isset($go['coordinates'][2])){
						$obj->appendChild($dom->createElement("ele", $go['coordinates'][2]));
					}
					if(strlen($go['name'])>0){ $obj->appendChild($dom->createElement("name", $go['name'])); }		
					if(strlen($go['desc'])>0){ $obj->appendChild($dom->createElement("desc", $go['desc'])); }		
				} else if($go['id_typeobject']==2) {
				
				
					if(strlen($go['name'])>0){ $obj->appendChild($dom->createElement("name", $go['name'])); }		
					if(strlen($go['desc'])>0){ $obj->appendChild($dom->createElement("desc", $go['desc'])); }
					$trkseg = $dom->createElement("trkseg");
					foreach($go['coordinates'] as $pt ){
						$trkpt = $dom->createElement("trkpt");
						if(isset($pt[2])){
							$trkpt->appendChild($dom->createElement("ele", $pt[2]));
						}
						$trkpt->setAttribute("lat", $pt[0]);
						$trkpt->setAttribute("lon", $pt[1]);
						$trkseg->appendChild($trkpt);
					}	
					$obj->appendChild($trkseg);
				}

				$gpx->appendChild($obj);
			}


			
			

		$dom->appendChild($gpx);
		header('Content-Description: File Transfer');
		header('Content-type: text/xml');
		header('Content-Disposition: attachment; filename=' . $name.".gpx");
		echo $dom->saveXML();
		

}else if(isset($_GET['id_layer'])){
	$q1=$mysqli->query("SELECT	ro.id, ro.id_route,ro.name,ro.`desc`,ro.coordinates,
			ro.`id_typeobject`,ro.`stroke_color`,ro.`stroke_opacity`,ro.`stroke_width`,ro.`distance`,ro.`id_creator`,ro.`date_create`,ro.`id_editor`,
			ro.`date_last_modif`
	FROM `route_objects` AS ro
	WHERE ro.id=".$_GET['id_layer']." LIMIT 1");
	if(!$q1){ die(json_encode(array("error"=>$mysqli->error)));}
	$r1=$q1->fetch_assoc();
	if($r1['id_typeobject']==2){ $result[]=json_decode($r1['coordinates']); }	
	$name = $r1['name'];		
	if(!strlen($name)>0){$name = "line_".date('dmYHis');}					
			
	
		
		$dom = new domDocument("1.0", "utf-8");
			$gpx = $dom->createElement("gpx");
			$gpx->setAttribute("xmlns", "http://www.topografix.com/GPX/1/1");
			$gpx->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
			$gpx->setAttribute("xsi:schemaLocation", "http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd");
			$gpx->setAttribute("creator", "http://pohodnik58.ru");
			$trk = $dom->createElement("trk");
			$trk->appendChild($dom->createElement("name", $name));
			foreach($result as $trkseg_src ){
				$trkseg = $dom->createElement("trkseg");
				foreach($trkseg_src as $trkpt_src ){
					$trkpt = $dom->createElement("trkpt");
					$trkpt->setAttribute("lat", $trkpt_src[0]);
					$trkpt->setAttribute("lon", $trkpt_src[1]);
					$trkseg->appendChild($trkpt);
				}
				$trk->appendChild($trkseg);
			}
			$gpx->appendChild($trk);
		$dom->appendChild($gpx);
		header('Content-Description: File Transfer');
		header('Content-type: text/xml');
		header('Content-Disposition: attachment; filename=' . $name.".gpx");
		echo $dom->saveXML();

} else{exit(json_encode(array("error"=>"Не определен маршрут для получения слоев")));}
/*






	$q = $mysqli->query("SELECT id, name, `desc` FROM  `route_collections` WHERE id_route={$id_route}");
	if($q){
		while($r=$q->fetch_assoc()){
		
$q1=$mysqli->query("SELECT	ro.id, ro.id_collection,ro.name,ro.`desc`,ro.hint,ro.coordinates,
										ro.`id_typeobject`,ro.`stroke_color`,ro.`stroke_opacity`,ro.`stroke_width`,ro.`distance`,
										ro.`id_icon`,ro.`is_draggable`,ro.`id_creator`,ro.`date_create`,ro.`id_editor`,
										ro.`date_last_modif`,
										route_icons.id AS icon_id,route_icons.name AS icon, 
										route_icons.icon_preview AS icon_preview,
										route_object_types.id AS type_id,route_object_types.value AS type_value,
										route_object_types.name AS type_name,route_object_types.icon AS type_icon
								FROM `route_objects` AS ro
									LEFT JOIN route_icons ON ro.id_icon = route_icons.id
									LEFT JOIN route_object_types ON ro.id_typeobject = route_object_types.id 
								WHERE ro.id_collection=".$r['id']." ORDER BY ro.date_create");
		}
	}

}else{exit(json_encode(array("error"=>"Ошибка при получении данных. \r\n".$mysqli->error)));}




 
 
  for ($i = 0; $i < count($logins); $i++) {
    $id = $i + 1; // id-пользователя
    $user = $dom->createElement("user"); // Создаём узел "user"
    $user->setAttribute("id", $id); // Устанавливаем атрибут "id" у узла "user"
    $login = $dom->createElement("login", $logins[$i]); // Создаём узел "login" с текстом внутри
    $password = $dom->createElement("password", $passwords[$i]); // Создаём узел "password" с текстом внутри
    $user->appendChild($login); // Добавляем в узел "user" узел "login"
    $user->appendChild($password);// Добавляем в узел "user" узел "password"
    $root->appendChild($user); // Добавляем в корневой узел "users" узел "user"
  }
   echo $doc->saveXML($doc->doctype); // Сохраняем полученный XML-документ в файл*/
?>