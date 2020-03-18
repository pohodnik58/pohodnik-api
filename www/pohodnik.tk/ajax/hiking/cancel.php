<?
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных

$res = array();
$id_hiking = intval($_POST['id_hiking']);
$id_user = isset($_POST['id_user'])?intval($_POST['id_user']):$_COOKIE["user"];
$comment = $mysqli->real_escape_string(trim($_POST['comment']));

if($id_user!=$_COOKIE["user"]){
	$q = $mysqli->query("SELECT id_author FROM hiking WHERE id = {$id_hiking} AND id_author=".$_COOKIE["user"]." LIMIT 1");
	if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
	if($q->num_rows===0){die(json_encode(array("error"=>"Вы не являетесь создателем похода")));}
} else {


$q = $mysqli->query("SELECT UNIX_TIMESTAMP(`date_finish`) FROM iv WHERE id_hiking = {$id_hiking} AND main=1");
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
$r=$q->fetch_row();
$deadline = $r[0];

if(time()>$deadline){
	die(json_encode(array("Error"=>"Deadline: ". date('d.m.Y H:i', $deadline ))));
}

echo date('d.m.Y H:i', $deadline );



}






$q = $mysqli->query("SELECT id FROM iv WHERE id_hiking = {$id_hiking}");
if(!$q){die(json_encode(array("error"=>$mysqli->error)));}
$ivs_id = array();
while($r=$q->fetch_row()){
	$ivs_id[] = $r[0];
}

$res["z"] = array();
$ans_ids = array();
if(count($ivs_id)>0){
	$q = $mysqli->query("SELECT iv_ans.id FROM iv_ans LEFT JOIN iv_qq ON iv_qq.id = iv_ans.id_qq  WHERE iv_qq.id_iv IN(".implode(',', $ivs_id).") AND iv_ans.id_user = {$id_user}");
	if(!$q){die(json_encode(array("error"=>$mysqli->error, "src"=>"iv_ans.id")));}
	while($r=$q->fetch_row()){
		$ans_ids[] = intval($r[0]);
	};
	
	$z = "DELETE FROM iv_ans_content WHERE id_ans IN(".implode(',', $ans_ids).")";
	$res["z"][] = $z;
	$q = $mysqli->query($z);
	if(!$q){die(json_encode(array("error"=>$mysqli->error, "src"=>"iv_ans_content")));}
	$res["iv_ans_content"] = $mysqli->affected_rows;




	
	$z = "DELETE FROM iv_ans WHERE id IN(".implode(',', $ans_ids).")";
	$res["z"][] = $z;
	$q = $mysqli->query($z);
	if(!$q){die(json_encode(array("error"=>$mysqli->error, "src"=>"iv_ans")));}
	$res["iv_ans"] = $mysqli->affected_rows;
	
	
	
}
	$q = $mysqli->query("DELETE FROM hiking_members WHERE id_user = {$id_user} AND id_hiking = {$id_hiking}");
	if(!$q){die(json_encode(array("error"=>$mysqli->error, "src"=>"equip")));}
	$res["hiking_members"] = $mysqli->affected_rows;
	
	$q = $mysqli->query("DELETE FROM hiking_equipment WHERE id_user = {$id_user} AND id_hiking = {$id_hiking}");
	if(!$q){die(json_encode(array("error"=>$mysqli->error, "src"=>"equip")));}
	$res["hiking_equipment"] = $mysqli->affected_rows;
	
	
	$q = $mysqli->query("INSERT INTO hiking_radish SET id_user = {$id_user}, id_hiking = {$id_hiking}, comment='{$comment}', date=NOW()");
	if(!$q){die(json_encode(array("error"=>$mysqli->error, "src"=>"equip")));}
	$res["success"] = true;	
	$res["iv"] =  $ivs_id ;
	$res["ans"] = $ans_ids ;
	echo json_encode($res);
	
/**/
?>