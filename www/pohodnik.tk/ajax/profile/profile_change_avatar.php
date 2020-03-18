<?php
ini_set('error_reporting', E_ALL);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$id_user = $_COOKIE["user"];
$ava_folder = 'avatars/'.$id_user;


function clear($folder) {
if (file_exists($folder))
foreach (glob($folder.'/*') as $file)
unlink($file);
}


$sizes = array(50,100,200);
/*
print_r($_FILES['file']);
exit();
*/
	if(!file_exists('../../'.$ava_folder)){
		mkdir('../../'.$ava_folder, 0777, true);
	chmod('../../'.$ava_folder, 0777);
	}
	
	
	$ava_folder = $ava_folder.'/';
	
	$a = explode (".", basename($_FILES['file']['name']));
	$uploadedFile =	str_replace('.'.$a[count($a)-1], "", basename($_FILES['file']['name']));
	$uploadedFile =  md5($uploadedFile.time()).'.'.$a[count($a)-1];
	if(is_uploaded_file($_FILES['file']['tmp_name'])){
		clear('../../'.$ava_folder);
		$path = '../../'.$ava_folder."orig_".$uploadedFile;
		if(move_uploaded_file($_FILES['file']['tmp_name'],$path)){
			list($width, $height) = getimagesize($path);
			//clear($ava_folder);
			
			$targ_w = $targ_h = 200;
			$jpeg_quality = 90;
			$img_r = imagecreatefromjpeg($path);
			$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
			imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
			$targ_w,$targ_h,$_POST['w'],$_POST['h']);
			imagejpeg($dst_r, '../../'.$ava_folder."s200_".$uploadedFile,$jpeg_quality);
			
			
			$targ_w = $targ_h = 100;
			$jpeg_quality = 90;
			$img_r = imagecreatefromjpeg($path);
			$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
			imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
			$targ_w,$targ_h,$_POST['w'],$_POST['h']);
			imagejpeg($dst_r, '../../'.$ava_folder."s100_".$uploadedFile,$jpeg_quality);

			
			$targ_w = $targ_h = 50;
			$jpeg_quality = 90;
			$img_r = imagecreatefromjpeg($path);
			$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
			imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
			$targ_w,$targ_h,$_POST['w'],$_POST['h']);
			imagejpeg($dst_r, '../../'.$ava_folder."s50_".$uploadedFile,$jpeg_quality);			
			
			
			$q=$mysqli->query("UPDATE `users` SET 
				`ava`='".$ava_folder."s200_".$uploadedFile."',
				`photo_50`='".$ava_folder."s50_".$uploadedFile."',
				`photo_100`='".$ava_folder."s100_".$uploadedFile."',
				`photo_200_orig`='".$ava_folder."s200_".$uploadedFile."',
				`photo_max`='{$path}',
				`photo_max_orig`='{$path}'
				WHERE id={$id_user}
			");
			if(!$q){exit(json_encode(array("error"=>"Ошибка\r\n".$mysqli->error)));}
			if($q){
				exit(json_encode(array("success"=>true,
					"ava"=>$ava_folder."s200_".$uploadedFile
				)));
			} else {
			
			}
			
		} else { 
			exit(json_encode(array("error"=> "Не могу переместить файл из временной папки")));
		}
	} else {
		exit(json_encode(array("error"=>"Файл не загружен ".$_FILES['file']['error'])));
	}
 

if(!$q){exit(json_encode(array("error"=>"Ошибка при редактировании пользовательских данных. \r\n")));}
echo(json_encode(array("msg"=>"Данные успешно обновлены. \r\n")));
?>
<?php /*
include("../blocks/db.php");
include("../img_resize/img_resize.php");

$sizes = array(50,100,200,300,400,500,600,700,800,900);
$id_work = intval($_POST['id_work']);
$result = array();
if(!(count($_FILES['file']['size'])>0)){die(json_encode(array('error'=>"nothing data")));}

for($i=0; $i<count($_FILES['file']['size']); $i++ ){
	if(!isset($_FILES['file'])){continue;}
	$data = array();
	
	$a = explode (".", basename($_FILES['file']['name']));
	$uploadedFile =	str_replace('.'.$a[count($a)-1], "", basename($_FILES['file']['name']));
	$uploadedFile =  md5($uploadedFile.time()).'.'.$a[count($a)-1];
	if(is_uploaded_file($_FILES['file']['tmp_name'])){
		$path = '../../images/original/'.$uploadedFile;
		if(move_uploaded_file($_FILES['file']['tmp_name'],$path)){
			list($width, $height) = getimagesize($path);
			$q = $mysqli -> query("INSERT INTO `pf_files` SET `name`='".($_FILES['file']['name'])."',`url`='{$uploadedFile}',`width`={$width},`height`={$height}");
			if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
			$copies = 0;
			for($j=0; $j<count($sizes); $j++){
				$koeff = $width / $height;
				if($width>=$sizes[$j]){
					if( g_webi_shop_img_resize($path, "../../images/".$sizes[$j]."/".$uploadedFile, $sizes[$j], round($sizes[$j]/$koeff),  100, 0xFFFFF0, 0,0,$sizes[$j]>500?'../img_resize/copyright/copyright_100.png':'')){
						$copies++;
					}
				}
			}
			
			$id_file = $mysqli->insert_id;
			if($id_work>0){
				$mysqli->query("INSERT INTO pf_work_files SET id_work={$id_work}, id_file={$id_file}");
			}
			
			$res[] = array(
				"id" =>	$id_file,
				"name" => $_FILES['file']['name'],
				"url"	=> $uploadedFile,
				"width" => $width,
				"height" => $height,
				"copies" => $copies
			);
		} else { 
			$res[] = array("error"=> "Не могу переместить файл из временной папки");
		}
	} else {
		$res[] = array("error"=>"Файл не загружен ".$_FILES['file']['error']);
	}
} 

echo json_encode($res);

*/
?>