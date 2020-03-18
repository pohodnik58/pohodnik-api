
'Все рецепты с кол-вом пожеланий. Без пожеланий сюда тоже попадают'
SELECT 
	food_acts.name AS act_name,
	food_acts.id AS act_id,
	GROUP_CONCAT( CONCAT(users.surname, ' ', users.name)) AS user_name,
	recipes.name,
	COUNT(DISTINCT users.id) AS cou
FROM recipes
	LEFT JOIN food_acts_recipe_categories ON food_acts_recipe_categories.id_recipe_category = recipes.id_category
	LEFT JOIN food_acts ON food_acts_recipe_categories.id_food_acts=food_acts.id	

	LEFT JOIN user_food_pref ON user_food_pref.id_recipe = recipes.id  AND user_food_pref.id_act =  food_acts.id
	LEFT JOIN hiking_members ON user_food_pref.id_user = hiking_members.id_user AND hiking_members.id_hiking=30
	LEFT JOIN users ON users.id = hiking_members.id_user
GROUP BY food_acts.id,recipes.id 
ORDER BY act_name, cou DESC;









SELECT 
	food_acts.name AS act_name,
	food_acts.id AS act_id,
	GROUP_CONCAT( CONCAT(users.surname, ' ', users.name)) AS user_name,
	recipes.name,
	COUNT(DISTINCT users.id) AS cou

FROM recipes
	
	LEFT JOIN food_acts_recipe_categories ON food_acts_recipe_categories.id_recipe_category = recipes.id_category
	LEFT JOIN food_acts ON food_acts_recipe_categories.id_food_acts=food_acts.id
	LEFT JOIN user_food_pref ON user_food_pref.id_recipe = recipes.id 
	LEFT JOIN hiking_members ON user_food_pref.id_user = hiking_members.id_user AND hiking_members.id_hiking=30
	LEFT JOIN users ON users.id = hiking_members.id_user

	LEFT JOIN hiking_recipes ON hiking_recipes.id_hiking=30 AND hiking_recipes.id_recipe=recipes.id
WHERE hiking_recipes.id_recipe IS NOT NULL
GROUP BY  food_acts.id,recipes.id 
ORDER BY act_name,cou DESC;



LEFT JOIN hiking_recipes ON hiking_recipes.id_hiking=hiking_members.id_hiking AND hiking_recipes.id_recipe=recipes.id


AND recipes.id IN (SELECT id_recipe FROM hiking_recipes WHERE id_hiking = 30) 




SELECT 
	hiking_schedule.d1,
	hiking_schedule.d2,
	hiking_schedule.name AS shedule_name,
	food_acts.name AS act_name,
	food_acts.id AS act_id,
	IF(hiking_schedule.kkal>0, hiking_schedule.kkal, food_acts.norm_kkal) AS kkal,
	IF(hiking_schedule.kkal>0,1,0) AS forcekkal,
	CONCAT(users.surname, ' ', users.name) AS user_name

	
FROM hiking_members
LEFT JOIN users ON users.id = hiking_members.id_user
LEFT JOIN hiking_schedule ON hiking_members.id_hiking = hiking_schedule.id_hiking
LEFT JOIN food_acts ON hiking_schedule.id_food_act=food_acts.id

WHERE hiking_schedule.id_hiking=27 AND hiking_schedule.id_food_act IS NOT NULL