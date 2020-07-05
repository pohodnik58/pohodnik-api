<?php
include("../../blocks/db.php");
include("../../blocks/for_auth.php");
include("../../blocks/global.php");
$result = array();

$q = $mysqli->query("SELECT
farc.*, rc.name as recipe_category_name, fa.name AS food_act_name
FROM `food_acts_recipe_categories` as farc
LEFT JOIN recipes_categories as rc ON rc.id = farc.id_recipe_category
LEFT JOIN food_acts as fa ON fa.id = farc.id_food_acts
    ORDER BY
    farc.id_food_acts, farc.order_index DESC
    ");
if(!$q) {
    die(err($mysqli->error));
}

$res = array();
while($r = $q -> fetch_assoc()) {
    $res[] = $r;
}

die(out($res));