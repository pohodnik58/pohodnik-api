<?
include("../../blocks/db.php"); //подключение к БД
if(!isset($_GET['code'])){die("Неверный код");}
$q = $mysqli->query("SELECT id, name,surname FROM users WHERE uniq_code='".$mysqli->real_escape_string($_GET['code'])."' LIMIT 1");

if($q && $q->num_rows===1){
	$r = $q->fetch_row();
	$user = $r[0];

	$avai = array('lat','lon','timestamp','time','hdop','alt','speed', 'comment');
	$ins = array();
	$comment = "";
	foreach($_GET as $k=>$v){
		if(in_array($k,$avai)){
			$comment .= "{$k}={$v}; ";
			if($k=='timestamp' || $k=='time'){
				if(!is_numeric($v)){
					$v = strtotime($v);
				}
				$v = date('Y-m-d H:i:s', $v>0?($v>99999999991?$v/1000:$v):time());
			}
			$ins[] = "`{$k}`='{$v}'";
		}
	}
	if(count($ins)>0){
		$ins[] = "`id_user`={$user}";
		$ins[] = "`comment`='{$comment}'";
		$q = $mysqli->query("INSERT INTO user_gps SET ".implode(', ', $ins));
		if(!$q){die($mysqli->error); }
		die(1);
	} 
	
	
} else {
	die("Не найден пользователь");
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?=$r[1]." ".$r[2];?></title>
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css"
   integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ=="
   crossorigin=""/>
   <style>
	html, body, #mapid { height: 100%; width:100%; margin:0; padding:0 }
	
.pulse {
  display: block;
  width: 10px;
  margin-left:-5px; margin-top:-5px;
  height: 10px;
  border-radius: 50%;
  background: rgba(255,0,0,0.9);
  cursor: pointer;
  box-shadow: 0 0 0 rgba(255,0,0, 0.4);
  animation: pulse 2s infinite;
}
.pulse:hover {
  animation: none;
}

@-webkit-keyframes pulse {
  0% {
    -webkit-box-shadow: 0 0 0 0 rgba(204,0,0, 0.4);
  }
  70% {
      -webkit-box-shadow: 0 0 0 56px rgba(204,0,0, 0);
  }
  100% {
      -webkit-box-shadow: 0 0 0 0 rgba(204,0,0, 0);
  }
}
@keyframes pulse {
  0% {
    -moz-box-shadow: 0 0 0 0 rgba(204,0,0, 0.4);
    box-shadow: 0 0 0 0 rgba(204,0,0, 0.4);
  }
  70% {
      -moz-box-shadow: 0 0 0 100px rgba(204,0,0, 0);
      box-shadow: 0 0 0 56px rgba(204,0,0, 0);
  }
  100% {
      -moz-box-shadow: 0 0 0 0 rgba(204,0,0, 0);
      box-shadow: 0 0 0 0 rgba(204,0,0, 0);
  }
}
   </style>
</head>
<body>
    
	<div id="mapid"></div>
	<script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js"
   integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw=="
   crossorigin=""></script>	
   
   <form style="z-index:55555; position:fixed; top:16px; right:16px">
		<input type="date" name="date" value="<?=isset($_GET['date'])?$_GET['date']:date('Y-m-d')?>">
		<input type="submit" value="↵">
   </form>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script>
	
	
	
L.Polyline = L.Polyline.include({
    getDistance: function(system) {
        // distance in meters
        var mDistanse = 0,
            length = this._latlngs.length;
        for (var i = 1; i < length; i++) {
            mDistanse += this._latlngs[i].distanceTo(this._latlngs[i - 1]);
        }
        // optional
        if (system === 'imperial') {
            return mDistanse / 1609.34;
        } else {
            return mDistanse / 1000;
        }
    }
});
	
	
	
	
	
	var map = L.map('mapid').setView([53.1202, 45.0016], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a rel="nofollow" href="https://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);
var mColors = "#F44336,#E91E63,#9C27B0,#673AB7,#3F51B5,#2196F3,#2196F3,#00BCD4,#009688,#4CAF50,#8BC34A,#CDDC39,#FFEB3B,#FFC107,#FF9800,#FF5722,#795548,#9E9E9E".split(',');
var latlngs = <?php
$q = $mysqli->query("SELECT lat, lon, DATE(timestamp) as date, unix_timestamp(timestamp) AS uts FROM user_gps WHERE id_user = {$user} AND DATE(timestamp)=DATE(".(isset($_GET['date'])?("'".$_GET['date']."'"):"NOW()").") ORDER BY timestamp, id");
$res = array();
if(!$q){die($mysqli->error);}

$i = 1;
$cur_date = "";
$last_uts = 0;
while($r=$q->fetch_assoc()){
	if($r['uts']-$last_uts>360){$i++; }
	if( $cur_date != $r['date']."-{$i}" ){ $cur_date = $r['date']."-{$i}";}
	
	if(!isset($res[$cur_date])){ $res[$cur_date] = array('latlon'=>array(), 'deadline'=>0); }
	$res[$cur_date]['latlon'][] = array($r['lat'], $r['lon']);
	$res[$cur_date]['deadline'] = $r['uts'];
	$last_uts=$r['uts'];
	
}
echo json_encode($res);
?>;
var polyline = {};
var deadline = 0;
var id_user = <?=$user;?>;
var k;
var prev_time;
for(k in latlngs){
	
	polyline[k] = L.polyline(latlngs[k]['latlon'], {color: mColors[Math.round(Math.random()*mColors.length-1)]}).addTo(map);	
	deadline = latlngs[k]['deadline']
		polyline[k].bindTooltip(k+';\r\n' );
		polyline[k].bindPopup('Dist ' + polyline[k].getDistance())
	
		
	
		
		prev_time = deadline;
	
	
}

var icon = L.divIcon({
	 className: 'map-marker',
	 iconSize:null,
	 html:'<div class="pulse">&nbsp;</div>'
});	
var currentPosMarker;
 if(k){
currentPosMarker = L.marker(latlngs[k]['latlon'][latlngs[k]['latlon'].length-1],{icon: icon})
currentPosMarker.addTo(map);	
					
map.fitBounds(polyline[k].getBounds());
console.log(new Date(deadline*1000), deadline)
 }
setInterval(function(){
	
	$.getJSON('/ajax/gps/watch_by_time.php',{id_user:id_user, uts:deadline}, function(res){
		if(res && res.length){
			var i;
			for(i=0; i<res.length; i++){
				deadline = res[i].uts
				if( polyline[res[i].date]){
					 polyline[res[i].date].addLatLng([res[i].lat, res[i].lon])

					if(currentPosMarker){
						  map.removeLayer(currentPosMarker)
					}
					currentPosMarker = L.marker([res[i].lat, res[i].lon],{icon: icon})
					currentPosMarker.addTo(map);					

				}
			}
			
			map.fitBounds(polyline[res[i].date].getBounds());
		}
	})
	
},5000)

	</script>
</body>
</html>

