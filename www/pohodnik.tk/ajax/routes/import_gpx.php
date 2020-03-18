<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
define('COORDINATES_FORMAT', 'WGS84'); 
define('MAJOR_AXIS', 6378137.0); //meters 
define('MINOR_AXIS', 6356752.3142); //meters 
define('MAJOR_AXIS_POW_2', pow(MAJOR_AXIS, 2)); //meters 
define('MINOR_AXIS_POW_2', pow(MINOR_AXIS, 2)); //meters 

	function get_distance_between_2_points($gps_1, $gps_2, $decart=false) 
	{ 
	    if(!$decart) 
	    { 
	        $true_angle_1 = get_true_angle($gps_1); 
	        $true_angle_2 = get_true_angle($gps_2); 
	         
	        $point_radius_1 = get_point_radius($gps_1, $true_angle_1); 
	        $point_radius_2 = get_point_radius($gps_2, $true_angle_2); 
	         
	        $earth_point_1_x = $point_radius_1 * cos(deg2rad($true_angle_1)); 
	        $earth_point_1_y = $point_radius_1 * sin(deg2rad($true_angle_1)); 
	         
	        $earth_point_2_x = $point_radius_2 * cos(deg2rad($true_angle_2)); 
	        $earth_point_2_y = $point_radius_2 * sin(deg2rad($true_angle_2)); 
	         
	        $x = get_distance_between_2_points(array('lat'=>$earth_point_1_x, 'lon'=>$earth_point_1_y), array('lat'=>$earth_point_2_x, 'lon'=>$earth_point_2_y), true); 
	        $y = pi() *  (  ($earth_point_1_x + $earth_point_2_x) / 360 ) * ( $gps_1['lon'] - $gps_2['lon'] ); 

	        return sqrt( pow($x,2) + pow($y,2) ); 
	    } 
	    else 
	    { 
	        return sqrt(pow(($gps_1['lat'] - $gps_2['lat']), 2) + pow(($gps_1['lon'] - $gps_2['lon']), 2)); 
	    } 
	} 

	//returns degree's decimal measure, getting degree, minute and second 
	function get_decimal_degree($deg=0, $min=0, $sec=0) 
	{ 
	    return ($deg<0) ? (-1*(abs($deg) + (abs($min)/60) + (abs($sec)/3600))) : (abs($deg) + (abs($min)/60) + (abs($sec)/3600)); 
	} 

	// get point, returns true angle 
	function get_true_angle($gps) 
	{ 
	    return atan(    (  (MINOR_AXIS_POW_2 / MAJOR_AXIS_POW_2) * tan(deg2rad( $gps['lat']))    )  ) * 180/pi();  
	} 

	//get point and true angle, returns radius of small circle (radius between meridians)  
	function get_point_radius($gps, $true_angle) 
	{ 
	    return (1 / sqrt((pow(cos(deg2rad($true_angle)), 2) / MAJOR_AXIS_POW_2) + (pow(sin(deg2rad($true_angle)), 2) / MINOR_AXIS_POW_2))) + $gps['point_elevation'];  
	} 



	function check_lat($lat) 
	{ 
	    if($lat>=0 && $lat<=90) 
	    { 
	        return 'north'; 
	    } 
	    else 
	    if($lat>=-90 && $lat<=0) 
	    { 
	        return 'south'; 
	    } 

	    return false; 
	} 

	function check_lon($lon) 
	{ 
	    if($lon>=0 && $lon<=180) 
	    { 
	        return 'east'; 
	    } 
	    else 
	    if($lon>=-180 && $lon<=0) 
	    { 
	        return 'west'; 
	    } 

	    return false; 

	} 

function dist($p1, $p2){



	return get_distance_between_2_points(
		array('lat'=>$p1[0],'lon'=>$p1[1],'point_elevation'=>$p1[2]),
		array('lat'=>$p2[0],'lon'=>$p2[1],'point_elevation'=>$p2[2])
	);

}

$result = array();
$id_hiking = isset($_POST['id_hiking'])?intval($_POST['id_hiking']):'NULL';
$id_user = isset($_COOKIE["user"])?$_COOKIE["user"]:0;
$name = isset($_POST['name'])?$_POST['name']:'';

if(isset($_FILES['import_gpx']) ){
	$track= simplexml_load_file($_FILES['import_gpx']['tmp_name']);
	if(!(strlen($name)>0)){
		$name = $mysqli->real_escape_string($track->metadata->name);
		if(!(strlen($name)>0)){ $name = basename($_FILES['import_gpx']['name']);}
	}

	$folder = "files/gpx/";
	$filename = md5(time())."u".$id_user."h".$id_hiking.".gpx";

	if (move_uploaded_file($_FILES['import_gpx']['tmp_name'], "../../".$folder.$filename)) {
	   
	} else {
	    echo "Возможная атака с помощью файловой загрузки!\n";
	}	
	
	$url = $folder.$filename;
	
	$date_start = 0;
	$date_finish = 0;
	$distance = 0;
	$speed_min = 1111;
	$speed_max = 0;
	$speed_avg = 0;
	$alt_min = 10000;
	$alt_max = 0;
	$alt_up_sum = 0;
	$alt_down_sum = 0;
	$time_in_moution = 0;
	$max_ele_dif = 0;


	$pres = isset($_POST['pres'])?intval($_POST['pres']):2;



	
	$points = array(); // [lat,lon,alt,uts]
	$res = array();

				$speed = 0;	
				$sumSpeed = 0;
				$couSpeed = 0;
				$prostoy = 0;




	if(isset($track->trk)){
		for($i=0; $i<count($track->trk->trkseg); $i++){
			$cou = count($track->trk->trkseg[$i]->trkpt);

				$tmp_dist = 0;
				$tmp_tim = 0;
				$tmp_alt = 0;

			for($j=0; $j<$cou; $j++){
				$p = $track->trk->trkseg[$i]->trkpt[$j];
				$lat = floatVal($p['lat']);
				$lon = floatVal($p['lon']);
				$ele = floatVal($p->ele);
				$time = strtotime($p->time);


				$points[] = array($lat,$lon,$ele,$time);
				if($j===0){
					$date_start = $time;
					$res[] = array(
						'p'=>array($lat,$lon,$ele,$time),
						'speed'=> 0,
						'dist'=> 0,
						'time'=>0,
						'd'=> date('d.m.Y H:i:s', $time)
					);
				}

				if($j>0){ 

					$dist = dist(array($lat,$lon,$ele,$time), $points[count($points)-2]); //расстояние в м. м-у текущ и предыдущ точками
					$tim = $time - ($points[count($points)-2][3]); // время в сек м-у текущей и предыдущей точками
					
					$distance = $distance + $dist; // Общее расстояние

					$tmp_dist = $tmp_dist + $dist;
					$tmp_tim  = $tmp_tim + $tim;
					$tmp_alt = $tmp_alt + $alt;

					if($tmp_tim>=10 || $j === $cou-1){ //

						$speed = $tmp_dist/$tmp_tim;	
						if($speed_min>=$speed){$speed_min=$speed;} 
						if($speed_max<=$speed){$speed_max=$speed;} 

						$sumSpeed = $sumSpeed+$speed;
						$couSpeed++;
						if($tmp_dist>=14){ //4km/h
							$time_in_moution = $time_in_moution + $tmp_tim;
						}

						$tmp_dist = 0;
						$tmp_tim = 0;	
						$tmp_alt = 0;

					}



					if($ele<$alt_min){$alt_min=$ele;}
					if($ele>$alt_max){$alt_max=$ele;}

					$ele_diff = $ele - $points[count($points)-2][2];
					if($ele_diff>0){$alt_up_sum = $alt_up_sum + $ele_diff;} else {$alt_down_sum = $alt_down_sum + abs($ele_diff);}
					if($ele_diff>$max_ele_dif){$max_ele_dif = $ele_diff;}


						$res[] = array(
							'p'=>array($lat,$lon,$ele,$time),
							'speed'=> $speed,
							'dist'=> $dist,
							'time'=>$tim,
							'd'=> date('d.m.Y H:i:s', $time),
							'tmp_dist'=>$tmp_dist,
							'tmp_tim'=>$tmp_tim
						);
						


				}

				

				if($j === ($cou-1) ){
					$date_finish = $time;
				}

			}

			
			
		}
		
	}
	
//	die(json_encode($res));
//	
//	$id_hiking = isset($_POST['id_hiking'])?intval($_POST['id_hiking']):'NULL';
//$id_user = isset($_COOKIE["user"])?$_COOKIE["user"]:0;
//$name = isset($_POST['name'])?$_POST['name']:''
//	
//	

	$q = $mysqli->query("INSERT INTO `hiking_tracks` SET 
		`id_user`={$id_user},
		`id_hiking`={$id_hiking},
		`url`='{$url}',
		`name`='{$name}',
		`date_create`=NOW(),
		`date_start`='".date('Y-m-d H:i:s', $date_start)."',
		`date_finish`='".date('Y-m-d H:i:s', $date_finish)."',
		`distance`={$distance},
		`speed_min`={$speed_min},
		`speed_max`={$speed_max},
		`speed_avg`=".($sumSpeed/$couSpeed).",
		`alt_min`={$alt_min},
		`alt_max`={$alt_max},
		`alt_up_sum`={$alt_up_sum},
		`alt_down_sum`={$alt_down_sum},
		`time_in_moution`={$time_in_moution}
	");
	if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
	die(json_encode(array(
		'distance'=>$distance, 
		'date_finish' => date('d.m.Y H:i', $date_finish),
		'date_start' => date('d.m.Y H:i', $date_start),
		'speed_min' => $speed_min*3.6,
		'speed_max' =>  $speed_max*3.6,
		'speed_avg' =>  ($sumSpeed/$couSpeed)*3.6,
		'points'=>$res,
		'time_in_moution'=>$time_in_moution,
		'time_in_moution_h'=>$time_in_moution/60/60,
		'prostoy'=>$prostoy,
		'alt_min'=>$alt_min,
		'alt_max'=>$alt_max,
		'alt_up_sum'=>$alt_up_sum,
		'alt_down_sum'=>$alt_down_sum,
		'max_ele_dif'=> $max_ele_dif

	)));
	
} else {
	die(json_encode(array('error'=>"import_gpx is not exists")));
}
?>
