<?php
include("../../blocks/db.php"); //подключение к БД
include("../../blocks/for_auth.php"); //Только для авторизованных
$result=array();
if(isset($_GET['id_hiking']) && $_GET['id_hiking']>0){
$id_hiking = $_GET['id_hiking'];
}

$id_user = $_COOKIE["user"];

$q = $mysqli->query("SELECT 
	hiking_food_list.id_act,
	hiking_food_list.id AS id_food_list,
	recipes_categories.id AS category_id,
	recipes_categories.name AS category_name,
	recipes.id, recipes.name, recipes.promo_text, recipes.id_author, (recipes.id_author=".$id_user.") AS my ,
	SUM((recipes_products.protein/100)*recipes_structure.amount) AS protein,
	SUM((recipes_products.fat/100)*recipes_structure.amount) AS fat,
	SUM((recipes_products.carbohydrates/100)*recipes_structure.amount) AS carbohydrates,
	SUM((recipes_products.energy/100)*recipes_structure.amount) AS energy,
	recipes_structure.amount
FROM hiking_food_list 
	LEFT JOIN recipes ON hiking_food_list.id_recipe=recipes.id 
	LEFT JOIN recipes_categories ON recipes.id_category = recipes_categories.id
	LEFT JOIN recipes_structure ON recipes_structure.id_recipe = recipes.id
	LEFT JOIN recipes_products ON  recipes_structure.id_product = recipes_products.id
WHERE hiking_food_list.id_hiking={$id_hiking} GROUP BY hiking_food_list.id ORDER BY hiking_food_list.id_act,hiking_food_list.order_index, recipes_categories.id");
if(!$q){exit(json_encode(array("error"=>"Ошибка при запросе. \r\n".$mysqli->error)));}


while($r = $q->fetch_assoc()){
	$result[] = $r;		
}

exit(json_encode($result));
?>