<?php
include("../../../blocks/db.php");
include("../../../blocks/for_auth.php");
include("../../../blocks/global.php");
$result = array();
$id_user = $_COOKIE["user"];
$id_hiking = isset($_GET['id_hiking'])?intval($_GET['id_hiking']):0;
if(!($id_hiking>0)){die(json_encode(array("error"=>"id_hiking is undefined")));}

$z = "
SELECT
    farc.*, rc.name as recipe_category_name, fa.name AS food_act_name
FROM `hiking_food_acts_recipe_categories` as farc
LEFT JOIN recipes_categories as rc ON rc.id = farc.id_recipe_category
LEFT JOIN food_acts as fa ON fa.id = farc.id_food_acts
WHERE farc.id_hiking={$id_hiking}
ORDER BY farc.id_food_acts, farc.order_index DESC
";
$q = $mysqli->query($z);
if(!$q) {
    die(err($mysqli->error, $z));
}

$res = array();
while($r = $q -> fetch_assoc()) {
    $res[] = $r;
}

die(out($res));