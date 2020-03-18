<?php
include("../../blocks/db.php");
$id = isset($_GET['id'])?intval($_GET['id']):36;
$color = isset($_GET['color'])?$mysqli->real_escape_string($_GET['color']):'#010002';
$q = $mysqli->query("SELECT `id`, `name`, `viewBox`, `paths` FROM `route_icons_svg` WHERE id={$id}");
$r = $q->fetch_assoc();



if(preg_match('/^[0-9a-fA-F]{6}|[0-9a-fA-F]{3}$/',$color)===1){
	$color = "#".$color;
}


	$gcontent = explode("|",$r['paths']);

	$dom = new domDocument("1.0", "utf-8");
		$svg = $dom->createElement("svg");
		$svg->setAttribute("version", "1.1");
		$svg->setAttribute("id", "pohodnik58_map_icon_"+ $r['id']);
		
		$svg->setAttribute("xmlns", "http://www.w3.org/2000/svg");
		$svg->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink");
		$svg->setAttribute("x", "0px");
		$svg->setAttribute("y", "0px");
		$svg->setAttribute("viewBox", $r['viewBox']);
		$svg->setAttribute("style", "enable-background:new ".$r['viewBox'].";");
		
		$g = $dom->createElement("g");
		
		
		
		foreach($gcontent as $p ){
		
			if(substr($p, 0, 1) === 'M'){
				$pp = $dom->createElement("path");
				$pp->setAttribute( "d", $p );
			} else {
				$pp = $dom->createElement("polygon");
				$pp->setAttribute( "points", $p );
			}
			$pp->setAttribute("style", "fill:{$color};");
			$g->appendChild($pp);
		}
		$svg->appendChild($g);
	$dom->appendChild($svg);

	header('Content-type: image/svg+xml');
	header('Vary: Accept-Encoding');
	echo $dom->saveXML();
?>