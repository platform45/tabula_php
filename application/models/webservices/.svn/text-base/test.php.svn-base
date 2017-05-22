<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

sql="select t.*,t1.*, t2.*,t3.*
              from
                (  SELECT
                  `u`.`user_id`,`u`.`latitude`,`u`.`longitude`,	restaurant_owner_name,  IF( u.user_image = '', '', CONCAT('http://192.168.21.7/tabula/', 'assets/upload/member/', u.	user_image) ) AS restaurant_image,
                  IF( u.restaurant_hero_image = '', '', CONCAT('http://192.168.21.7/tabula/', 'assets/upload/member/', u.restaurant_hero_image) ) AS restaurant_hero_image,
                  CONCAT( u.user_first_name,' ',u.user_last_name ) as restaurant_name,
                  
                  u.average_spend,
                  u.user_email,
                  u.user_contact,
                  u.user_description as restaurant_description,
                  cou.cou_name as country,
                  reg.region_name as state,
                  c.city_name as city,
                  cou.cou_id as country_id,
                  reg.region_id as state_id,
                  c.city_id as city_id,
                  u.street_address1 as address,
                  dr.diet_id as diet_id,
                  dp.diet_preference
                FROM
                  (`tab_usermst` u)
                JOIN
                  `tab_dietary_restaurant` dr
                    ON `dr`.`user_id` = `u`.`user_id`
                  JOIN
                  `tab_dietary_preference` dp
                    ON `dp`.`diet_id` = `dr`.`diet_id`
                JOIN
                  `tab_city` c
                    ON `c`.`city_id` = `u`.`city_id`
                JOIN
                  `tab_region` reg
                    ON `reg`.`region_id` = `u`.`region_id`
                JOIN
                  `tab_country` cou
                    ON `cou`.`cou_id` = `u`.`country_id`
                WHERE
                  `u`.`user_id` =  '31'
                  AND `u`.`user_type` =  '3'
                  AND `u`.`user_status` =  '1'
                  AND `u`.`is_deleted` =  '0'
                GROUP BY
                  `dr`.`user_id`) as t
              JOIN
                (
                  SELECT
                    `u`.`user_id`,
                    IFNULL(ROUND(avg((service_rating + ambience_rating + food_quality_rating + value_for_money_rating)/4),
                    2),
                    0) AS average_rating,
                    COUNT( NULLIF( your_thoughts, '' ) ) AS average_review
                  FROM
                    (`tab_usermst` u)
                  LEFT JOIN
                    `tab_rating` r
                      ON `r`.`restaurant_id` = `u`.`user_id`
                      AND r.status = '1'
                      AND r.is_approved = '1'
                  WHERE
                    `u`.`user_id` =  '31'
                    AND `u`.`user_type` =  '3'
                    AND `u`.`user_status` =  '1'
                    AND `u`.`is_deleted` =  '0'
                  group by
                    r.`restaurant_id`
                ) as t1
                  ON t.`user_id` = t1.`user_id`                
                  
                JOIN
                (
                  SELECT
                  `u`.`user_id`,
                  GROUP_CONCAT(case rca.rca_type
                    when '2' then a.ambience_name
                  end ) as ambience,
                  GROUP_CONCAT(case rca.rca_type
                    when '2' then a.ambience_id
                  end ) as ambience_id
                FROM
                  (`tab_usermst` u)
                JOIN
                  `tab_restaurant_cuisine_ambience` rca
                    ON `rca`.`user_id` = `u`.`user_id`
                JOIN
                  `tab_ambience` a
                    ON `a`.`ambience_id` = `rca`.`rca_cuisine_ambience_id`
                WHERE
                  `u`.`user_id` =  '31'
                  AND `u`.`user_type` =  '3'
                  AND `u`.`user_status` =  '1'
                  AND `u`.`is_deleted` =  '0'
                GROUP BY
                  `rca`.`user_id`
                ) as t2
                  ON t1.`user_id` = t2.`user_id`
                  
JOIN
                (
                  SELECT
                  `u`.`user_id`,
                  GROUP_CONCAT(case rca.rca_type
                    when '1' then cu.cuisine_name
                  end ) as cuisine,
                  GROUP_CONCAT(case rca.rca_type
                    when '1' then cu.cuisine_id
                  end ) as cuisine_id
                FROM
                  (`tab_usermst` u)
                JOIN
                  `tab_restaurant_cuisine_ambience` rca
                    ON `rca`.`user_id` = `u`.`user_id`
                JOIN
                  `tab_cuisine` cu
                    ON `cu`.`cuisine_id` = `rca`.`rca_cuisine_ambience_id`
                WHERE
                  `u`.`user_id` =  '31'
                  AND `u`.`user_type` =  '3'
                  AND `u`.`user_status` =  '1'
                  AND `u`.`is_deleted` =  '0'
                GROUP BY
                  `rca`.`user_id`
                ) as t3
                  ON t1.`user_id` = t3.`user_id`
?>
