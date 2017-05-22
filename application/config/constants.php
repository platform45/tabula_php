<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

define('MAX_UPOAD_VIDEO_SIZE', 602400000);
define('MAX_UPOAD_IMAGE_SIZE', 5048);
define('MAX_UPOAD_PDF_SIZE', 10000);
define('ADMIN_MENU_ICONS', 'http://' . $_SERVER['HTTP_HOST'] . '/assets/images/icons/');
define('ADMIN_USER_IMAGE_PATH', 'assets/upload/adminuser/');
define('RESTAURANT_IMAGE_PATH', 'assets/upload/restaurant/');
define('SLIDER_IMAGE_PATH', 'assets/upload/slider/');
define('BRAND_IMAGE_PATH', 'assets/upload/brand/');
define('CHANNEL_ICON_PATH', 'assets/upload/icon/');
define('CATEGORY_ICON_PATH', 'assets/upload/category_icon/');
define('NEWS_IMAGE_PATH', 'assets/upload/news/');
define('SLIDER_HEADER_PATH', 'assets/upload/header/');
define('EVENT_GALLERY_PATH', 'assets/upload/eventgallery/');
define('VIDEO_PATH', 'assets/video/');
define('ADS_PATH', 'assets/ads/');
define('CONTENT_PDF_FILE', 'assets/upload/pdf/');
define('SITE_TITLE', 'Tabula: ');
define('MEMBER_IMAGE_PATH', 'assets/upload/member/');
define('FOODMENU_IMAGE_PATH', 'assets/upload/foodmenu/');
define('GALLERY_IMAGE_PATH', 'assets/upload/gallery/');
define('SUBADMIN_IMAGE_PATH', 'assets/upload/subadmin/');
define('promotion_IMAGE_PATH', 'assets/upload/promotion/');
define('PROMOTION_PDF_PATH', 'assets/upload/promotion_pdf/');
define('SUBSCRIBER_CSV_PATH', 'assets/upload/subscriber/');

//  suggestion search minimum distance
define('SUGGESSTIONS_DISTACE', 10);

//constant for admin URL
define('ADMIN', 'admin/');
// page limit for channels

define('PAGE_LIMIT', 20);

// define videos for each category on channel listing

define('VIDEO_LIST', 6);
// push type of app side

define('PUSH_IS_CHANNEL_ADDED', 1);
define('PUSH_IS_VIDEO_ADDED', 2);


// push messages of app side
define('CHANNEL_MESSAGE', "New channel is added {name}");
define('VIDEO_MESSAGE', "New Video is added {name}");

// pem file for ios
// define('PEM_FILE','assets/certificate/Certificates.pem');
// define('PASSPHRASE','P@123');

// // API KEY for push
// define('GOOGLE_API_KEY','AIzaSyCd3ToZ36ayLMvb0rpJdGKP3_oUPseD_4k');


// define('SSL_URL','ssl://gateway.sandbox.push.apple.com:2195');


// -----------Constant for operating days and hours-------------
define('OPERATING_DAYS', 'Monday-Sunday');
define('OPERATING_HOURS', "6:00AM - 1:00AM");

//Search
define('SEARCH_RESULTS_LIMIT', 10);
define('SEARCH_ADMIN_TYPE', 0);
define('SEARCH_SUBADMIN_TYPE', 1);
define('SEARCH_APP_USER_TYPE', 2);
define('SEARCH_RESTAURANT_TYPE', 3);


define('SEARCH_MAX_PRICE', 1000);

// Booking
define('PERMITTED_GUEST_NUMBER', 20);
define('REQUESTED_BOOKING', 1);
define('CONFIRMED_BOOKING', 2);
define('CANCELLED_BOOKING', 3);
define('BOOKING_CONFIRMED', 3);
define('ALL_BOOKING', 4);


define('FUTURE_BOOKING', 1);
define('HISTORY_BOOKING', 2);

// 1rand == 10 points
define('FIXED_LOYALTY_POINT', 10);

// Constants for webservices
define('TOKEN', 'onTlucAMel97Bo3N');

define('SUCCESS', '1');
define('FAIL', '0');

define('DEVICE_TYPE', 'A,I');

//Token
define('VALID_TOKEN_MESSAGE', 'Valid token');
define('INVALID_TOKEN_MESSAGE', 'Sorry, you can not proceed further as the app has been logged in with this credential in some other device. Please login to continue.');
define('NO_TOKEN_MESSAGE', 'Token not provided');
define('TOKEN_GENERATION_SUCCESS', 'New token generated.');

//Search webservice
define('SEARCH_SUCCESS', 'Search results found.');
define('SEARCH_FAILED', 'No results found.');
define('EMPTY_SEARCH_INPUT', 'Please select some criteria for detail search.');

// Login webservice
define('INVALID_USER_CREDENTIALS', 'Invalid email or password.');
define('VALID_USER_CREDENTIALS', 'Login successfully.');
define('EMPTY_INPUT', 'Opps, Something went wrong with details.');
define('INVALID_EMAIL', 'Please enter a valid email.');
define('INVALID_DEVICE_TYPE', 'Invalid device');

define('EMPTY_CUISINE', 'Please select atleast one cuisine');
define('EMPTY_AMBIENCE', 'Please select atleast one amenities');
define('EMPTY_DIETARY_PREFERENCE', 'Please select atleast one dietary preference');

// Forgot Password
define('EMAIL_NOT_FOUND', 'Email not found.');
define('ACCOUNT_INACTIVE', 'Sorry, your account awaits admin approval.');
define('EMAIL_SEND_FAILED', 'Something went wrong, could not send mail.');
define('EMAIL_SENT', 'Email sent successfully.');

//-----------MESSAGES RELATED TO COUNTRIES-------------
define('NO_COUNTRIES', 'No countries found.');
define('ALL_COUNTRIES', 'All countries found.');

//-----------MESSAGES RELATED TO STATES-------------
define('NO_STATES', 'No states found.');
define('ALL_STATES', 'All states found.');

//-----------MESSAGES RELATED TO CITIES-------------
define('NO_CITIES', 'No cities found.');
define('ALL_CITIES', 'All cities found.');

//-----------MESSAGES RELATED TO Registration-------------
define('EMAIL_EXISTS', 'The email already exists.');
define('REGISTRATION_FAILED', 'Registration failed.');
define('REGISTRATION_SUCCESS', 'Hey, you have successfully registered on Tabula.');
define('COUNTRY_NOT_EXISTS', 'The country does not exists.');
define('STATE_NOT_EXISTS', 'The state does not exists.');
define('CITY_NOT_EXISTS', 'The city does not exists.');
define('FILE_UPLOAD_FAILED', 'Sorry, failed to upload file.');
define('PDF_FILE_UPLOAD_FAILED', 'Sorry, failed to upload  pdf file.');
define('INVALID_ADDRESS', 'Please enter valid physical address.');
define('CUISINE_NOT_EXISTS', 'The cuisine does not exists.');
define('AMBIENCE_NOT_EXISTS', 'The ambience does not exists.');

//-----------MESSAGES RELATED TO CUISINES-------------
define('NO_CUISINES', 'No cuisines found.');
define('ALL_CUISINES', 'All cuisines found.');

//-----------MESSAGES RELATED TO NEWS-------------
define('NO_NEWS', 'No news found.');
define('ALL_NEWS', 'News found.');

//-----------MESSAGES RELATED TO Promotions-------------
define('NO_PROMOTIONS', 'No promotions found.');
define('ALL_PROMOTIONS', 'Promotions found.');
define('ADD_PROMOTIONS', 'Promotion added successfully.');
define('UNABLE_ADD_PROMOTIONS', 'Unable to add promotion.');

//-----------MESSAGES RELATED TO ADS -------------
define('NO_ADS', 'No ads found.');
define('ALL_ADS', 'Ads found.');

//-----------MESSAGES RELATED TO STATIC COUNTRY ID -------------
define('STATIC_COUNTRY_ID', '47');


//-----------MESSAGES RELATED TO AMBIENCE-------------
define('NO_AMBIENCE', 'No ambience found.');
define('ALL_AMBIENCE', 'All ambience found.');

//-----------MESSAGES RELATED TO Dietary Preferences-------------
define('NO_DIETARY', 'No Dietary Preference found.');
define('ALL_DIETARY', 'All Dietary Preference found.');

//-----------MESSAGES RELATED TO LOGOUT-------------
define('LOGOUT_SUCCESS', "You have logged out successfully.");

//-----------MESSAGES RELATED TO PROFILE-------------
define('INVALID_USER', 'Invalid user.');
define('VALID_USER', 'Valid user details found.');
define('VALID_CONTACTS', 'Contact list found.');
define('EDIT_SUCCESS', 'Profile updated successfully.');
define('EDIT_FAILED', 'Something went wrong while updating your profile.');

//-----------MESSAGES RELATED TO RESTAURANT-------------
define('VALID_MENU', 'Menu images found.');
define('MENU_NOT_FOUND', 'Menu images not found.');
define('INVALID_RESTAURANT', 'Invalid restaurant.');
define('EMPTY_RATING_INPUT', 'Please select atleast one rating or enter review.');
define('INVALID_RATING', 'Please select appropriate rating.');
define('RATING_SUCCESS', 'You have successfully rated the restaurant.');
define('RATING_FAILED', 'Rating submission failed.');
define('VALID_RATING', 'Rating details found.');
define('RATING_NOT_FOUND', 'Rating details not found.');
define('WISHLIST_STATUS_ADDED', 'Added to wishlist');
define('WISHLIST_STATUS_REMOVED', 'Removed from wishlist');
define('WISHLIST_FOUND', 'Wishlist found succesfully');
define('WISHLIST_NOT_FOUND', 'No restaurant found');
define('RESTAURANT_NOT_FOUND', 'No restaurant found');

//-----------MESSAGES RELATED TO CONTENT-------------
define('VALID_CONTENT', 'Content found.');
define('INVALID_CONTENT', 'Content not present.');


//-----------MESSAGES RELATED TO Suggestions-------------
define('VALID_SUGGESTIONS', 'Suggestion found.');
define('INVALID_SUGGESTIONS', 'No suggestion found.');


//-----------MESSAGES RELATED TO GALLERY-------------
define('GALLERY_FOUND', 'Gallery images found.');
define('NO_GALLERY_FOUND', 'No gallery images found.');
define('ADD_GALLERY', 'Gallery images added succesfully.');
define('UNABLE_ADD_GALLERY', 'No gallery images found.');


//-----------MESSAGES RELATED TO FOODMENU-------------

define('ADD_MENU', 'Menu image added succesfully.');
define('UNABLE_ADD_MENU', 'No menu images found.');


//-----------MESSAGES RELATED TOP 10 RESTAUARANTS-------------

define('TOP_10_RESTAURANTS', 'Top 10 restaurants found');

//-----------MESSAGES RELATED TO BOOKING-------------
define('VALID_USERS_LIST', 'Users found.');
define('NO_USERS', 'No users found.');
define('INVALID_GUESTS', 'Guest data incorrect.');
define('INVALID_GUEST_NUMBER', 'Only 20 guests permitted.');
define('BOOKING_FAILED', 'Booking failed.');
define('BOOKING_UPDATE_SUCCESS', 'Booking updated successfully.');
define('BOOKING_UPDATE_FAILED', 'Unable to update booking. Please try again after sometime.');
//define('BOOKING_SUCCESS','Your table has been provisionally reserved will shortly send your confirmation.');
define('BOOKING_SUCCESS', 'Your table has been provisionally reserved.');
define('REQUEST_EXISTS', 'You have already requested for a table.');

define('INVALID_BOOKING_TYPE', 'Request for invalid booking type.');
define('VALID_BOOKING_LIST', 'Bookings found.');
define('NO_BOOKING', 'No bookings found.');
define('NO_HISTORY', 'No History Found.');

define('INVALID_BOOKING', 'Invalid booking selected.');
define('INVALID_TABLE', 'Invalid table selected.');
define('BOOKING_DELETE_SUCCESS', 'Booking deleted.');
define('BOOKING_DELETE_FAILED', 'Error while deleting booking.');
define('BOOKING_STATUS_SUCCESS', 'Booking status changed successfully.');
define('BOOKING_STATUS_FAILED', 'Error while updating booking status.');

//-----------MESSAGES RELATED TO TABLES-------------
define('NO_TABLES', 'No tables found.');
define('ALL_TABLES', 'All tables found.');

//-----------MESSAGES RELATED TO PUSH NOTIFICATIONS-------------
// For ios
define('DEV_PEM_FILE', 'assets/certificate/tabula_dev.pem');
define('DEV_PASSPHRASE', '123456');
define('DEV_SSL_URL', 'ssl://gateway.sandbox.push.apple.com:2195');

define('LIVE_PEM_FILE', 'assets/certificate/tabula_dev.pem');
define('LIVE_PASSPHRASE', '123456');
define('LIVE_SSL_URL', 'ssl://gateway.sandbox.push.apple.com:2195');

// For android
//define('DEV_FIREBASE_API_KEY','AIzaSyCKVdM-sVkQUaHP4kAdDzQWyuMUDZowM5E');
define('DEV_FIREBASE_API_KEY', 'AAAAW4rifZ4:APA91bF77pkSMKoMEFMI2htyINfvfoC64cSCT6p-3ei3F7b_fG4mTnnSa6wbV5AhB2D0D7B3lW5W5jZR1-Lbqyxnilh5bvqvm7vD-XVeLsQ1P2AO9VhnzyRaa56ifRYVL79OMtVTGLV2');
define('LIVE_FIREBASE_API_KEY', 'AAAAW4rifZ4:APA91bF77pkSMKoMEFMI2htyINfvfoC64cSCT6p-3ei3F7b_fG4mTnnSa6wbV5AhB2D0D7B3lW5W5jZR1-Lbqyxnilh5bvqvm7vD-XVeLsQ1P2AO9VhnzyRaa56ifRYVL79OMtVTGLV2');

// Message and type constants
define('BOOKING_CONFIRMED_TYPE', '2');
define('BOOKING_WAITING_TYPE', '6');
define('BOOKING_REJECTED_TYPE', '4');
define('BOOKING_CANCELLED_TYPE', '5');
define('BOOKING_BILL_SENT_TYPE', '7');
define('PAYMENT_RECEIVED_TYPE', '8');
define('LOYALTY_POINTS_RECEIVED_TYPE', '9');
define('LAST_MINUTE_CANCELLATION_TYPE', '10');
define('NEWS_TYPE', '11');
define('PROMOTION_TYPE', '12');

define('BOOKING_CONFIRMED_MESSAGE', 'Booking confirmed. Tables booked: {tables}');
define('LAST_MINUTE_CANCELLATION_MESSAGE', 'xyz');
define('BOOKING_WAITING_MESSAGE', 'Booking moved to waiting list.');
define('BOOKING_REJECTED_MESSAGE', 'Booking rejected.');
define('BOOKING_CANCELLED_MESSAGE', 'Booking cancelled.');
define('BOOKING_BILL_SENT_MESSAGE', 'Bill is sent. Please pay it.');
define('PAYMENT_RECEIVED_MESSAGE', 'Bill of amount: {amount} is received from table: {tables}.');
define('LOYALTY_POINTS_RECEIVED_MESSAGE', 'Loyalty points received: {amount}.');

define('BOOKING_CONFIRMED_SUBJECT', 'Booking confirmation');
define('LAST_MINUTE_CANCELLATION_SUBJECT', 'XYZ');
define('BOOKING_WAITING_SUBJECT', 'Booking moved to waiting list.');
define('BOOKING_REJECTED_SUBJECT', 'Booking rejected.');
define('BOOKING_CANCELLED_SUBJECT', 'Booking cancelled.');
define('BOOKING_BILL_SENT_SUBJECT', 'Please pay your bill.');
define('PAYMENT_RECEIVED_SUBJECT', 'Bill received.');
define('LOYALTY_POINTS_RECEIVED_SUBJECT', 'You have received loyalty amount.');

define('VALID_LOYALTY', 'Loyalty points found.');
define('VALID_HOURS', 'Operating hours found.');

//-----------MESSAGES RELATED TO PAYMENT-------------
define('PAYMENT_SUCCESS', 'Payment records found.');
define('PAYMENT_FAIL', 'No payment records found.');
define('AMOUNT_SUCCESS', 'Bill sent.');
define('AMOUNT_FAIL', 'Error while sending bill.');
define('LOYALTY_UNAVAILABLE', 'Loyalty points used are more than available.');
define('LOYALTY_EXCEED', 'Loyalty points used exceed the payable amount.');
define('MAKE_PAYMENT_SUCCESS', 'Payment received.');
define('MAKE_PAYMENT_FAIL', 'Error while paying bill.');


////Notification webservice
define('NOTIFICATION_SUCCESS', 'Notification results found.');
define('NOTIFICATION_FAILED', 'No notification(s) found.');
define('INVALID_NOTIFICATION', 'Invalid notification selected.');
define('NOTIFICATION_DELETE_SUCCESS', 'Notification deleted.');
define('NOTIFICATION_DELETE_FAILED', 'Error while deleting notification.');

// client_zone
define('CLIENT_ZONE', 'Africa/Johannesburg');


// Restaurant limit 
define('RESTAURANT_SEARCH_LIMIT', '12');
define('LAST_MINUIT_NOTIFICATION_LIMIT', '12');

//Difference Time slots
define('TIME_SLOT_DIFFERECNCE', '12');
define('NO_TIME_SLOT_TODAY', 'No time slot available');
define('AVAILABLE_TIME_SLOT_TODAY', 'Available time slot for today');

//Delete restaurant menu
define('DELETE_MENU_FAILED', 'Sorry! Unable to delete this menu.');
define('DELETE_MENU_SUCCESS', 'Menu deleted successfully.');

//delete restaurant gallery image
define('DELETE_GALLERY_IMAGE_FAILED', 'Sorry! Unable to delete this image.');
define('DELETE_GALLERY_IMAGE_SUCCESS', 'Image deleted successfully.');
define('TIME_SLOT_SUCCESS', 'Available time slots.');

define('BOOKING_FOUND', 'Booking found.');
define('BOOKING_CANCELLED', 'Your booking has been cancelled.');
define('LAST_MINUTE_CANCELLED_NOTIFICATION_NOT_FOUND', 'No notification found.');
define('LAST_MINUTE_CANCELLED_NOTIFICATION_FOUND', 'Last minute cancellation notification found.');
define('TABLE_ALREADY_BOOKED', 'Sorry for inconvenience, The table you have selected is already booked.');

define('NOT_LOG_IN', 'Please login first.');



/* End of file constants.php */
/* Location: ./application/config/constants.php */