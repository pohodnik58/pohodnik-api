<? 
include("../../blocks/db.php"); //подключение к БД
$q = $mysqli->query("SELECT * FROM recipes_categories");
if(!$q){exit(json_encode(array("error"=>"Ошибка при получении списка категорий. \r\n".$mysqli->error)));}

while($r = $q->fetch_assoc()){ 
	$result[] = $r;
}
echo json_encode($result);
 
?>