<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "Welcome";
$route['404_override'] = '';

$route['admin'] = 'admin/home/index/';
$route['admin/'] = 'admin/home/index/';

$route['myaccount'] = 'admin/user/';

$route['admin/users'] = 'admin/members';
$route['admin/users/addedit/(:any)'] = "admin/members/addedit/$1";
$route['admin/users/addedit'] = "admin/members/addedit/";
$route['admin/'] = 'admin/home/index/';

$route['menucard'] = 'menucard/index';
$route['menucard/(:any)'] = 'menucard/index/$1';

$route['admin/tables'] = 'admin/restaurant_table';
$route['admin/tables/addedit'] = 'admin/restaurant_table/addedit';
$route['admin/tables/addedit/(:any)'] = 'admin/restaurant_table/addedit/$1';
$route['admin/tables/delete_table/(:any)'] = 'admin/restaurant_table/delete_table/$1';
$route['admin/tables/update_table_status'] = 'admin/restaurant_table/update_table_status';

$route['restaurant/(:any)'] = 'front/restaurant/index/$1';
$route['restaurant-details/(:any)'] = 'front/restaurant/restaurant_details/$1';
$route['search'] = 'front/restaurant/search/';
$route['news'] = 'front/news_front/index';
$route['news_details/(:any)'] = 'front/news_front/news_details/$1';
$route['admin/check_duplicate_title/(:any)'] = 'admin/news/check_duplicate_title/$1';

$route['load_news'] = 'front/restaurant/load_news/';
$route['restaurants_filter_options'] = 'front/restaurant/restaurants_filter_options/';
$route['fetch_restaurants_by_search_filter'] = 'front/restaurant/fetch_restaurants_by_search_filter/';
$route['fetch_restaurants_by_name'] = 'front/restaurant/fetch_restaurants_by_name/';
$route['restaurant_add_to_wish_list'] = 'front/restaurant/restaurant_add_to_wish_list/';
$route['restaurant_remove_from_wish_list'] = 'front/restaurant/restaurant_remove_from_wish_list/';
$route['add_review'] = 'front/restaurant/add_review/';
$route['top_ten_restaurant'] = 'front/restaurant/search_top_ten/';
$route['home'] = 'front/home';
$route['user_login'] = 'front/home/login';
$route['forget_password'] = 'front/home/forget_password';
$route['restaurant_get_updated_reviews'] = 'front/restaurant/restaurant_get_updated_reviews';

$route['home'] = 'front/home/index/$1';
$route['about-us'] = 'front/home/index/$1';
$route['contact-us'] = 'front/home/index/$1';
$route['terms-and-condition'] = 'front/home/index/$1';
$route['privacy-policy'] = 'front/home/index/$1';

$route['profile'] = 'front/profile/index/';
$route['profile/get_confirm_booking_list'] = 'front/profile/get_confirm_booking_list/';
$route['profile/modify_booking/(:any)'] = 'front/profile/modify_booking/$1';
$route['profile/booking_by_slot_id/(:any)'] = 'front/profile/booking_by_slot_id/$1/$2';
/* End of file routes.php */
/* Location: ./application/config/routes.php */