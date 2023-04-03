<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

/** Run Cron Job Url **/
Route::get('closeEventLogout', 'AttendanceController@closeEventLogout');
Route::get('ExpiredEventTokenCronJob', 'CronJobController@ExpiredEventTokenCronJob');
/** End Cron Job Url **/

Route::get('/updatemembercode', 'UserController@updatemembercode');

// Route::get('/', 'HomeController@index')->name('home');
//Route::get('/', 'UserController@index');
Route::get('/', 'UserController@MemberList');
/*Langauage Route START */
Route::get('locale/{locale}', function ($locale) {
	Session::put('locale', $locale);
	return redirect()->back();
});
/*Langauage Route END */

/*Autentcation Route START */
Route::group(['namespace' => 'Auth'], function () {
	Route::get('logout', 'LoginController@logout')->name('logout');
	Route::match(['GET', 'POST'], 'login', 'LoginController@index')->name('login');
	Route::match(['GET', 'POST'], 'loginCheck', 'LoginController@logincheck')->name('loginCheck');
	Route::match(['GET', 'POST'], 'check-qr-login', 'LoginController@CheckQrLogin')->name('check-qr-login');
	Route::match(['GET', 'POST'], 'register', 'RegisterController@index')->name('register');
	Route::match(['GET', 'POST'], 'registerUser', 'RegisterController@register')->name('registerUser');
	Route::match(['GET', 'POST'], 'check-email-not-register', 'LoginController@checkEmailRegister')->name('emailnotregister');
	Route::match(['GET', 'POST'], 'check-email-register', 'RegisterController@EmailCheckRegister')->name('emailregister');
	Route::match(['GET', 'POST'], 'forgetPassword', 'ForgotPasswordController@forgetPassword')->name('forgetPassword');
	Route::match(['GET', 'POST'], 'resetPassword', 'ForgotPasswordController@resetPassword')->name('resetPassword');
});
/* Autentcation Route END */
Route::group(['middleware' => 'login'], function () {

	/* User Management Route Start*/
	Route::get('members','UserController@MemberList')->name('members');

	Route::resource('user-management','UserManagementController');
	Route::post('change-user-password','UserManagementController@changePassword');
	Route::get('user-management/delete/{id}','UserManagementController@destroy');

	/*Member Management Route START*/
	Route::get('users/delete/{id}', 'UserController@destroy');
	Route::match(['GET', 'POST'], 'history-team-rank', 'UserController@historyTeamRank')->name('history-team-rank');
	Route::match(['GET', 'POST'], 'check-email', 'UserController@checkEmail')->name('check-email');
	Route::match(['GET', 'POST'], 'check-contact-number', 'UserController@checkContactnumber')->name('check-contact-number');
	Route::match(['GET', 'POST'], 'check-chinese-name', 'UserController@checkChinesename')->name('check-chinese-name');
	Route::match(['GET', 'POST'], 'check-english-name', 'UserController@checkEnglishname')->name('check-english-name');

	Route::match(['GET', 'POST'], 'members-list', 'UserController@membersList')->name('members-list');
	Route::post('exportCSV', 'UserController@exportCSV');

	/* Import Routes Start*/ 
	Route::match(['GET','POST'],'import-users','ImportController@importMember')->name('import-users');
	Route::match(['GET','POST'],'import-events','ImportController@importEvent')->name('import-events');
	Route::match(['GET','POST'],'import-roles','ImportController@importRole')->name('import-roles');
	Route::match(['GET','POST'],'import-teams','ImportController@importTeam')->name('import-teams');
	Route::match(['GET','POST'],'import-sub-teams','ImportController@importSubTeam')->name('import-sub-teams');
	Route::match(['GET','POST'],'import-ranks','ImportController@importRank')->name('import-ranks');
	Route::match(['GET','POST'],'import-qualification','ImportController@importQualification')->name('import-qualification');
	Route::match(['GET','POST'],'import-related-activity-history','ImportController@importActivityHistory')->name('import-related-activity-history');
	Route::match(['GET','POST'],'import-speciality','ImportController@importSpeciality')->name('import-speciality');
	Route::match(['GET','POST'],'import-remarks','ImportController@importRemark')->name('import-remarks');
	Route::match(['GET','POST'],'import-event-type','ImportController@importEventType')->name('import-event-type');
	Route::match(['GET','POST'],'import-award-assign-member-list','ImportController@importAwardBadges')->name('import-award-assign-member-list');
	Route::match(['GET','POST'],'import-badge-assigned-member-list','ImportController@importBadgesMember')->name('import-badge-assigned-member-list');
	/* Import Routes End*/ 

	/*Export Routes Start*/
	Route::post('export-assign-award-member','ExportController@exportAssignedAwardMembers')->name('export-assign-award-member');
	/*Export Routes Start*/

	Route::get('users/remarksData/{id}', 'UserController@remarksData');
	Route::get('users/remarkseditData/{id}', 'UserController@remarkseditData');
	Route::get('users/elitedata/{id}', 'UserController@elitedata');
	Route::post('event_type_serach', 'UserController@event_type_serach');
	Route::post('ajax_LoadMoreattendanceList', 'UserController@LoadMoreattendanceList');
	Route::get('get-all-event', 'UserController@get_all_event');
	Route::post('get_event_type', 'UserController@get_event_type');
	Route::post('get_event_post_type', 'UserController@get_event_post_type');
	Route::post('get_product_cost_type', 'UserController@get_product_cost_type');
	Route::post('get_child_product_prefix_suffix', 'UserController@get_child_product_prefix_suffix');
	Route::post('update-status', 'UserController@update_status');
	Route::post('multiple-user-update-status', 'UserController@multiple_user_update_status');
	Route::resource('users', 'UserController');
	Route::get('checkUserIsMentorTeam','UserController@checkUserIsMentorTeam');
	Route::post('member/deleteDocument', 'UserController@ajaxDeleteDocument');
	/*Member Management Route END*/

	/*Role Management Route START*/
	Route::get('/roleManagement/delete/{id}', 'RoleManagementController@destroy');
	Route::resource('roleManagement', 'RoleManagementController');
	/*Role Management Route END*/

	Route::match(['GET', 'POST'], 'language', 'LanguageController@Language')->name('language');

	/*Event Management Route START*/
	Route::get('event/delete/event-post-type/{post_type}', 'EventController@deletePostType');
	Route::get('event-get', 'EventController@Eventget');
	Route::post('reRescheduleEvent', 'EventController@reRescheduleEvent');
	Route::post('recurringevent', 'EventController@recurringEvent');
	Route::post('editNewEventDates', 'EventController@editNewEventDates');
	Route::post('submitEvent', 'EventController@submitEvent');
	Route::get('eventstatusUpdate/{id}', 'EventController@eventstatusUpdate');
	Route::match(['GET', 'POST'], 'event-report/{id}', 'EventController@EventReport')->name('event-report');
	Route::match(['GET', 'POST'], 'event-assign-user', 'EventController@eventAssignUser')->name('event-report');
	Route::get('/eventManagement/delete/{id}', 'EventController@destroy');
	Route::get('/eventManagement/deleteEventSchedule/{id}', 'EventController@deleteEventSchedule');
	Route::get('recurringeditevent/edit/{id}/{date}', 'EventController@recurringeventEdit');
	Route::post('recurringeventUpdate', 'EventController@recurringeventUpdate');
	Route::post('event-list-search', 'EventController@eventListSearch');
	Route::resource('eventManagement', 'EventController');
	Route::post('event/generateEventCode', 'EventController@generateEventCode');

	Route::post('event/setPostType', 'EventController@setPostType');
	/*Event Management Route END*/

	/*Service Hour Package Route START*/
	Route::resource('service-hour-package', 'ServiceHourPackageController');
	/*Service Hour Package Route END*/

	/*Attendance Route START*/
	Route::match(['GET', 'POST'], 'recordAttendance', 'AttendanceController@recordAttendance')->name('recordAttendance');
	Route::match(['GET', 'POST'], 'recordMemberCodeAttendance', 'AttendanceController@recordMemberCodeAttendance')->name('recordMemberCodeAttendance');
	Route::match(['GET', 'POST'], 'attendance-report', 'AttendanceController@attendanceReport')->name('attendance-report');
	Route::match(['GET', 'POST'], 'event-report-search', 'AttendanceController@attendancesearchReport')->name('event-report-search');
	Route::get('attendance-report-detail/{id}', 'AttendanceController@attendancereportdetail');
	//Route::get('getEventAttenderList/{event_id}/{date}/{type}/{status}', 'AttendanceController@getEventAttenderList');
	Route::get('getEventAttenderList', 'AttendanceController@getEventAttenderList');

	Route::get('getEventTypeList','AttendanceController@getEventTypeList');

	Route::get('generateQRCode/{id}', 'AttendanceController@generateQRCode');
	Route::post('attendance-event-list-search', 'AttendanceController@attendanceEventListSearch');
	Route::post('attendance-event-list-search-date', 'AttendanceController@attendanceEventListSearchDate');
	Route::get('/attendanceManagement/delete/{id}', 'AttendanceController@destroy');
	Route::post('attendance-event-list', 'AttendanceController@attendanceEventList');
	Route::resource('attendanceManagement', 'AttendanceController');
	/*Attendance Route END*/

	/*Assign User Report Start*/
	Route::match(['GET', 'POST'], 'assign-user-report', 'ReportController@assignUserReport')->name('assign-user-report');
	Route::match(['GET', 'POST'], 'remarks-update', 'ReportController@remarksUpdate')->name('remarks-update');
	Route::post('member/eventmeber', 'ReportController@ajaxGetUserList');
	Route::post('member/getEventMember', 'ReportController@getEventMember');
	Route::post('member/deleteAssignMember', 'ReportController@deleteAssignMember');
	Route::post('member/assignMember', 'ReportController@assignMember');
	Route::post('member/assignMemberFromView', 'ReportController@assignMemberFromView');
	Route::post('report/getEventFromType', 'ReportController@getEventFromType');
	Route::get('eventAssignStatusUpdate/{id}', 'ReportController@eventAssignStatusUpdate');
	Route::get('selecteventAssignStatusUpdate', 'ReportController@selecteventAssignStatusUpdate');
	Route::get('delete-enrollment-event-member', 'ReportController@deleteEnrollmentEventMember');

	/*Assign User Report End*/

	/* Product Assign User Report Start*/
	Route::match(['GET', 'POST'], 'product-assign-user-report', 'ProductReportController@assignUserReport')->name('product-assign-user-report');
	Route::match(['GET', 'POST'], 'product-remarks-update', 'ProductReportController@remarksUpdate')->name('product-remarks-update');
	Route::get('product/enrolment_order/member_list', 'ProductReportController@getEnrollmenetProductAssignedMembers');
	Route::post('product/assignMemberFromView', 'ProductReportController@assignMemberFromView');
	Route::get('selectproductAssignStatusUpdate', 'ProductReportController@selectproductAssignStatusUpdate');
	Route::get('productAssignChangeStatusUpdate/{id}', 'ProductReportController@productAssignChangeStatusUpdate');
	Route::get('delete-all-members-product-assigned', 'ProductReportController@deleteAllMembersProductAssigned');
	Route::post('product/deleteAssignMember', 'ProductReportController@deleteAssignMember');
	Route::post('product/eventmeber', 'ProductReportController@ajaxGetUserList');
	Route::post('product/assignMember', 'ProductReportController@assignMember');
	Route::get('product/delete/enrollment-product','ProductReportController@deleteEnrollmentProduct');
	/* Product Assign User Report End*/

	/*Qualification Route START*/
	Route::get('qualification/delete/{id}', 'QualificationController@destroy');
	Route::resource('qualification', 'QualificationController');
	/*Qualification Route END*/

	/*Qualification Route START*/
	Route::get('related-activity-history/delete/{id}', 'RelatedActivityController@destroy');
	Route::resource('related-activity-history', 'RelatedActivityController');
	/*Qualification Route END*/

	/*Specialty Route START*/
	Route::get('specialty/delete/{id}', 'SpecialtyController@destroy');
	Route::resource('specialty', 'SpecialtyController');
	/*Specialty Route END*/

	/*Elite Route START*/
	Route::get('elite/delete/{id}', 'EliteController@destroy');
	Route::resource('elite', 'EliteController');
	/*Elite Route END*/

	/*Team Route START*/
	Route::get('team/delete/{id}', 'TeamController@destroy');
	Route::resource('team', 'TeamController');
	/*Team Route END*/

	/*Rank Route START*/
	Route::get('rank/delete/{id}', 'RankController@destroy');
	Route::resource('rank', 'RankController');
	/*Rank Route END*/

	/*Rank Route START*/
	Route::get('event-type/delete/{id}', 'EventTypeController@destroy');
	Route::resource('event-type', 'EventTypeController');
	/*Rank Route END*/

	/*SubElite Route START*/
	Route::get('subelite/delete/{id}', 'SubEliteController@destroy');
	Route::resource('subelite', 'SubEliteController');
	/*SubElite Route END*/

	/*SubElite Route START*/
	Route::get('subteam/delete/{id}', 'SubTeamController@destroy');
	Route::resource('subteam', 'SubTeamController');

	/*Remarks Route START*/
	Route::get('remarks/delete/{id}', 'RemarksController@destroy');
	Route::resource('remarks', 'RemarksController');
	/*Remarks Route END*/

	/*purchase-product Route START*/
	Route::get('purchase-product/delete/{id}', 'PurchaseproductController@destroy');
	Route::post('search-purchase-product', 'PurchaseproductController@SearchPurchaseProduct');
	Route::resource('purchase-product', 'PurchaseproductController');
	/*purchase-product Route END*/

	/*Product Route START*/
	Route::get('product/delete/product-cost-type/{cost_type}', 'ProductController@deleteCostType');
	Route::get('product/delete/product-suffix', 'ProductController@deleteProductSuffix');
	Route::get('product/delete/{id}', 'ProductController@destroy');
	Route::post('product/removeImage', 'ProductController@removeImage');
	Route::get('product-list', 'ProductController@productList');
	Route::get('cart', 'ProductController@cartProduct');
	Route::get('checkout', 'ProductController@checkoutProduct');
	Route::post('checkout-update', 'ProductController@checkoutCartUpdate');
	Route::post('add-to-cart', 'ProductController@addToCart');
	Route::post('remove-cart-product', 'ProductController@removeCartProduct');
	Route::post('product-qty-change', 'ProductController@productQtyChange');
	Route::post('add-order', 'ProductController@addOrder');
	Route::get('addMoreProduct/{id}', 'ProductController@addMoreProduct');
	Route::get('get-all-product', 'ProductController@get_all_product');
	Route::get('product/enrollment-order-list', 'ProductController@getProductEnrollmentList');
	Route::post('product-assign-user', 'ProductController@productAssignUser');
	Route::post('product/enrollment-order/assigned-member', 'ProductController@AssignedProductEnrollmentMembers');

	Route::get('productAssignStatusUpdate/{id}', 'ProductController@productAssignStatusUpdate');
	Route::post('product/addRemark', 'ProductController@addRemark');
	Route::get('product-history', 'ProductController@productHistory')->name('product-history');
	Route::get('product/get-suffix-option-list', 'ProductController@getProductSuffixList')->name('product.get-suffix-list');
	Route::resource('product', 'ProductController');
	/*Product Route END*/

	/*Audit Log Route START*/
	Route::match(['GET', 'POST'], 'audit-log', 'AuditLogController@index')->name('audit-log');
	Route::match(['GET', 'POST'], 'audit-log/show/{id}', 'AuditLogController@show');
	Route::get('audit-log/delete/{id}', 'AuditLogController@audtilogDelete');
	/*Audit Log Route END*/

	Route::match(['GET', 'POST'], 'changepassword', 'HomeController@changepassword')->name('changepassword');
	Route::match(['GET', 'POST'], 'profile', 'HomeController@userProfile')->name('profile');

	// Site Setting
	Route::get('setting', 'SettingController@siteSetting')->name('setting');
	Route::post('settings/update', 'SettingController@update')->name('settings/update');

	// Filter Graph data
	Route::post('filterGraph', 'HomeController@filterGraph')->name('filterGraph');

	//Token Log report
	Route::get('transaction-history', 'AttendanceController@transactionHistory')->name('transaction-history');

	//Adjustment of token
	Route::get('token-management', 'AttendanceController@tokenManagement')->name('token-management');
	Route::get('token-management/edit/{id}', 'AttendanceController@editToken')->name('token-management.edit');
	Route::post('token-management/update/{id}', 'AttendanceController@updateToken')->name('token-management.update');

	//cron job for expired token
	Route::get('expiredToken', 'AttendanceController@expiredToken');

	Route::get('checkQR', 'AttendanceController@checkQR');

	// Size-Attributes Modules Routes
	Route::resource('size-attributes', 'SizeAttributesController');

	// Categories Modules Routes
	Route::resource('categories', 'CategoriesController');

	// Badges Module Routes
	Route::get('getBadgeCategoriesByTeamMember','BadgesController@getBadgeCategoriesByTeamMember')->name('getBadgeCategoriesByTeamMember');
	Route::resource('badges', 'BadgesController');
	Route::get('get-all-badgelist','BadgesController@getAllBadgelist')->name('get-all-badgelist');
	Route::post('badge-assign-user','BadgesController@badgeAssignUser')->name('badgeAssignUser');
	Route::get('badge-assigned-member-list','BadgesController@badgeAssignedMemberList')->name('badge-assigned-member-list');
	Route::get('getBadgesCategoriesList','BadgesController@getBadgesCategoriesList')->name('getBadgesCategoriesList');
	Route::get('getBadgesCategoriesByMentorType','BadgesController@getBadgeCategoriesByTeamMember')->name('getBadgesCategoriesByMentorType');

	// Awards Module Routes
	Route::resource('awards', 'AwardsController');
	Route::get('get-all-awardlist','AwardsController@getAllAwardlist')->name('get-all-awardlist');
	Route::get('getAwardCategoriesList','AwardsController@getAwardCategoriesList')->name('getAwardCategoriesList');
	Route::post('award-assign-user','AwardsController@awardAssignUser')->name('awardAssignUser');
	Route::get('award-assigned-member-list','AwardsController@awardAssignedMemberList')->name('award-assigned-member-list');

	// Awards & Badges categories Module Routes
	Route::get('getAwardsBadgeCategoriesOptions','AwardsBadgesCategoriesController@getAwardsBadgeCategoriesOptions')->name('getAwardsBadgeCategoriesOptions');
	Route::resource('awards-badges-categories', 'AwardsBadgesCategoriesController');

	// Export routes
	Route::get('export/events', 'ExportController@events');
	Route::get('export/award-assign-member-list','ExportController@awardAssignMember');
	Route::get('export/attendance-list', 'ExportController@attendanceList');
	Route::get('export/assign-user-report','ExportController@assignUserReport');
	Route::get('export/assignedevents/members','ExportController@assignedEventsMemberList');
	Route::get('export/roles', 'ExportController@roles');
	Route::get('export/tokens', 'ExportController@Tokens');
	Route::get('export/product', 'ExportController@Product');
	Route::get('export/enrollment_product', 'ExportController@EnrollmentProduct');
	Route::get('export/attendance-transaction','ExportController@AttendanceTransaction');
	Route::get('export/attendance-event-member','ExportController@AttendanceEventMember');
	Route::get('export/product-history','ExportController@productHistory');
	Route::get('export/badge-assign-member','ExportController@badgeAssignMember');
	Route::post('export/member-qrcodes-url', 'ExportController@exportMemberQrCodeUrls')->name('exportMemberQrCodeUrls');
});