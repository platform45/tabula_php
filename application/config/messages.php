<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Custom messages for status and replies from webservices.
|--------------------------------------------------------------------------
|
| These messages are used whenever an operation is executed and status messages are to be returned.
|
*/

// WEBSERVICE MESSAGES
define('SUCCESS','1');
define('FAIL','0');

//-----------MESSAGES RELATED TO TOKEN-------------
define('INVALID_TOKEN_MESSAGE','Invalid API token.');
define('NO_TOKEN_MESSAGE','No API token received.');
define('VALID_TOKEN_MESSAGE','API Token validated.');

//-----------MESSAGES RELATED TO LOGIN, REGISTRATION AND EDIT PROFILE-------------
define('INVALID_USER_CREDENTIALS','Invalid username or password.');
define('VALID_USER_CREDENTIALS','Login successfully.');
define('INVALID_USER','Invalid user.');
define('INVALID_USER_ID','Invalid user id.');
define('SAME_USER_ID','Both user Id cannot be same.');
define('VALID_USER','Valid user details found.');
define('INVALID_EMAIL','Please enter a valid email.');
define('IMAGE_UPLOAD_FAIL','Unable to upload image.');
define('DEVICE_CLEARED','Device details cleared.');

define('REGISTRATION_FAILED','Registration failed.');
define('REGISTRATION_SUCCESS','Hey, You are successfully register on safyer.');

define('PROFILE_UPDATE_FAILED','Failed to update profile.');
define('PROFILE_UPDATE_SUCCESS','Profile updated successfully.');

define('USER_EXISTS','The user already exists.');
define('EMAIL_EXISTS','The email already exists.');
define('USERNAME_EXISTS','The username already exists.');
define('CONTACT_EXISTS','The contact number already exists.');
define('USERNAME_AND_EMAIL_EXISTS','The username and email already exists.');

//-----------MESSAGES RELATED TO COUNTRIES-------------
define('NO_COUNTRIES','No countries found.');
define('ALL_COUNTRIES','All countries found.');

//-----------MESSAGES RELATED TO CHANGE PASSWORD-------------
define('PASSWORD_MISMATCH','Old password is invalid.');
define('PASSWORD_MATCH','Old password is correct.');
define('PASSWORD_UPDATE_FAILED','Failed to update password.');
define('PASSWORD_UPDATE_SUCCESS','Profile updated password.');

//-----------MESSAGES RELATED TO FORGET PASSWORD-------------
define('EMAIL_NOT_FOUND','Email not found.');
define('ACCOUNT_INACTIVE','Sorry, your account awaits admin approval.');
//define('EMAIL_SEND_FAILED','Something went wrong, could not send mail.');
//define('EMAIL_SENT','Email sent successfully.');

//-------------------Get Gallery data---------------
define('GALLERY_NOT_FOUND','No record found.');
define('GALLERY_FOUND','Gallery images found.');

//-------------------Get Event data---------------
define('EVENT_NOT_FOUND','No record found.');
define('EVENT_FOUND','success.');

//-----------MESSAGES RELATED TO MEMBERS-------------
define('NO_MEMBERS','No member(s) found.');
define('ALL_MEMBERS','Member(s) found.');

//-----------MESSAGES RELATED TO Categories-------------
define('SUCCESS_CATEGORIES','success');
define('NO_CATEGORIES','No record found');
define('FAIL_CATEGORIES','Something went wrong');


//-----------MESSAGES RELATED TO Categories-------------
define('SUCCESS_CHANNELS','success');
define('NO_CHANNELS','No record found');
define('FAIL_CHANNELS','Something went wrong');


//-----------MESSAGES RELATED TO Videos-------------
define('SUCCESS_VIDEOS','success');
define('NO_VIDEOS','No record found');
define('FAIL_VIDEOS','Something went wrong');



//-----------MESSAGES RELATED TO MEMBERS-------------
define('NO_FRIENDS','No friends found.');
define('ALL_FRIENDS','Friend(s) found.');

//-----------MESSAGES RELATED TO WALL-------------
define('INVALID_POST','Invalid post.');
define('POST_SUCCESSFUL','Post successful.');
define('POST_FAILED','Post failed.');
define('LIKE_SUCCESSFUL','Like successful.');
define('LIKE_FAILED','Like failed.');
define('COMMENT_SUCCESSFUL','Comment successful.');
define('COMMENT_FAILED','Comment failed.');
define('NOT_SHARED_WALL','The member has not shared wall with you.');

//-----------MESSAGES RELATED TO MEMBERS-------------
define('NO_POSTS','No posts found.');
define('ALL_POSTS','Posts found.');

//-----------MESSAGES RELATED TO COUNT-------------
define('COUNT_RETURNED','Count for all the entities returned.');

//-----------MESSAGES RELATED TO USER SETTINGS-------------
define('INSUFFICIENT_DETAILS','Insufficient details received.');
define('SETTINGS_UPDATE_FAILED','Failed to update user settings.');
define('SETTINGS_UPDATED_SUCCESSFULLY','User settings updated successfully.');

//-----------MESSAGES RELATED TO GEOZONE-------------
define('NO_RANGES_FOUND','No ranges found.');
define('ALL_RANGES_FOUND','All ranges found.');
define('NO_GEOZONES_FOUND','No geozones found.');
define('ALL_GEOZONES_FOUND','All geozones found.');
define('GEOZONE_ADDITION_FAILED','Geozone addition failed.');
define('GEOZONE_ADDITION_SUCCESSFUL','Geozone added successful.');
define('GEOZONE_UPDATE_FAILED','Geozone update failed.');
define('GEOZONE_UPDATE_SUCCESSFUL','Geozone update successful.');
define('MEMBER_REMOVED_FROM_GEOZONE','Member successfully removed from geozone.');
define('MEMBERS_FOUND','Members found.');
define('MEMBERS_NOT_FOUND','Members not found.');
define('NO_GEOZONE_DATA_FOUND','No geozone details found.');
define('GEOZONE_DATA_FOUND','Geozone details found.');
define('GEOZONE_DELETED_SUCCESSFULLY','Geozone deleted successfully.');
define('GEOZONE_DELETE_FAILED','Geozone delete failed.');

//-----------MESSAGES RELATED TO MAP-------------
define('NO_MAP_USERS_FOUND','No users found on map.');
define('ALL_MAP_USERS_FOUND','Users found on map.');
define('LOCATION_UPDATE_FAIL','User location update failed.');
define('LOCATION_UPDATE_SUCCESS','User location updated successfully.');

//-----------MESSAGES RELATED TO CONTENT-------------

define('CONTENT_FOUND','Content found.');
define('CONTENT_NOT_FOUND','Content not found.');
define('MAIL_SENT','Mail sent successfully.');
define('MAIL_NOT_SENT','Could not send mail. Please try after some time.');

//-----------MESSAGES RELATED TO EVENT-------------
define('EVENT_SUCCESSFUL','Event added successfully.');
define('EVENT_FAILED','Failed to add event.');
define('EVENT_EDIT_SUCCESSFUL','Event updated successfully.');
define('EVENT_EDIT_FAILED','Failed to update event.');
define('NOT_SHARED_CALENDAR','The member has not shared calendar with you.');
define('NO_EVENTS','No events found.');
define('ALL_EVENTS','Events found.');
define('REQUEST_ACCEPTED_SUCCESSFULLY','Request accepted successfully.');
define('REQUEST_DECLINED_SUCCESSFULLY','Request declined successfully.');
define('REQUEST_PENDING','Request status changed to pending.');
define('FAILED_TO_CHANGE_STATUS','Failed to update request status.');
define('EVENT_DELETE_SUCCESS','Event deleted successfully.');
define('EVENT_DELETE_FAILED','Event delete falied.');

//-----------MESSAGES RELATED TO NOTIFICATION-------------
define('NO_NOTIFICATIONS','No notifications found.');
define('ALL_NOTIFICATIONS','Notifications found.');
define('NOTIFICATIONS_DELETED','Notification deleted successfully.');
define('NOTIFICATIONS_DELETE_FAILED','Notification delete failed.');
define('NOTIFICATIONS_STATUS_CHANGED','Notification status changed successfully.');
define('NOTIFICATIONS_STATUS_CHANGE_FAILED','Notification status change failed.');

define('JINX_REQUEST_SENT_NOTIFICATION','{NAME} sent you a friend request.');
define('JINX_REQUEST_ACCEPTED','{NAME} accepted your friend request.');
define('JINX_REQUEST_REJECTED','{NAME} rejected your friend request.');
define('JINX_ADDED_TO_GEOZONE','{NAME} added you to his geozone list.');
define('JINX_INVITED_TO_EVENT','{NAME} invited you to Event {EVENT_NAME}.');
define('JINX_ACCEPTED_EVENT_REQUEST','{NAME} accepted your request for Event {EVENT_NAME}.');
define('JINX_REJECTED_EVENT_REQUEST','{NAME} rejected your request for Event {EVENT_NAME}.');
define('JINX_LIKED_A_POST','{NAME1} {COUNT} liked {NAME2} post.');
define('JINX_COMMENTED_ON_A_POST','{NAME1} {COUNT} commented on {NAME2} post.');
define('JINX_ENTERED_TO_GEOZONE','{NAME} entered in your geozone.');
define('JINX_MOVED_OUT_TO_GEOZONE','{NAME} left your geozone.');


//-----------MESSAGES RELATED TO CHAT-------------
define('BLOCK_FAILED','Failed to block the user.');
define('BLOCK_SUCCESS','User blocked successfully.');
define('UNBLOCK_SUCCESS','User unblocked successfully.');
define('MESSAGE_SEND_SUCCESS','Message sent successfully.');
define('MESSAGE_SEND_FAILED','Message sending failed.');
define('NO_MESSAGES','No message(s) found.');
define('ALL_MESSAGES','Message(s) found.');
define('NO_MESSAGES_DELETED','No messages deleted.');
define('ALL_MESSAGES_DELETED','Message(s) deleted.');
define('USER_BLOCKED','You are blocked by {NAME}.');
// ADMIN MESSAGES



/* End of file messages.php */
/* Location: ./application/config/messages.php */