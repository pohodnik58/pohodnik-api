<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result = array();
$id_route = intval($_POST['id_route']);
$user = isset($_COOKIE["user"])?$_COOKIE["user"]:0;

if(isset($_FILES['import_gpx']) ){
	$track= simplexml_load_file($_FILES['import_gpx']['tmp_name']);
	
	$name = $mysqli->real_escape_string($track->metadata->name);
	$desc = $mysqli->real_escape_string($track->metadata->description);
	
	if(!(strlen($name)>0)){$name =   basename($_FILES['import_gpx']['name']);}
	


	
	$res = "INSERT INTO `route_objects` (`id_route`, `name`, `desc`, `coordinates`, `id_typeobject`, `id_creator`, `date_create`, `id_editor`, `date_last_modif`) VALUES \r\n";
	
	
	$qss = array();
	
	$points = array();
	for($i=0; $i<count($track->wpt); $i++){
		$points[] = "({$id_route}, '".$track->wpt[$i]->name."',  '".$track->wpt[$i]->cmt."',  '[".$track->wpt[$i]['lat'].",".$track->wpt[$i]['lon']."]', 1, {$user}, NOW(), {$user}, NOW())";
		
	}
	if(count($points)>0){$qss[]= implode(",\r\n",$points);}


	$lines = array();
	for($i=0; $i<count($track->rte); $i++){
		$line = array();
		foreach($track->rte[$i]->rtept as $p ){
			$line[] = "[".$p['lat'].",".$p['lon']."]";
		}
		$lines[] = "({$id_route}, '".$track->rte[$i]->name."',  '".$track->rte[$i]->cmt."',  '[".implode(",",$line)."]', 2, {$user}, NOW(), {$user}, NOW())";
	}
	if(count($lines)>0){$qss[] = implode(",\r\n",$lines);}

	
	
	
	$trks = array();
	if(isset($track->trk)){
		
		for($i=0; $i<count($track->trk->trkseg); $i++){
			$line = array();
			foreach($track->trk->trkseg[$i]->trkpt as $p ){
				$line[] = "[".$p['lat'].",".$p['lon']."]";
			}
			$trks[] = "({$id_route}, 'trkseg{$i}',  'trkseg{$i}',  '[".implode(",",$line)."]', 2, {$user}, NOW(), {$user}, NOW())";
		}
		if(count($trks)>0){$qss[] = implode(",\r\n",$trks);}
	}
	
	if(count($points)>0 || count($lines)>0 || count($trks)>0){
	
		$res .= implode(",\r\n",$qss);
		
		
		
		
		$q = $mysqli->query($res);
		if(!$q){die(json_encode(array('error'=>$mysqli->error, "q"=>$res)));}
		die(json_encode(array('success'=>true)));
	}
	
} else {
	die(json_encode(array('error'=>"import_gpx is not exists")));
}
?>
