<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DemoCustomerController;
use App\Http\Controllers\ResellerController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\PointHistoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
// Route::controller(AuthController::class)->group(function () {
//     Route::post('login', 'login');
//     Route::post('register', 'register');
//     Route::post('logout', 'logout');
//     Route::post('refresh', 'refresh');

// });

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::group(['middleware' => ['jwt.verify']], function () {

    Route::post('changePassword', [AuthController::class, 'changePassword']);
    Route::post('updateUser', [AuthController::class, 'updateUser']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('add_point', [AuthController::class, 'addPoint']);
    Route::post('reseller_point', [AuthController::class, 'reversePoint']);
    Route::get('delete_user/{id}', [AuthController::class, 'deleteUser']);


    Route::post('all-data', [IndexController::class, 'getAllData']);
    Route::post('all-user', [IndexController::class, 'getAllUser']);
    Route::post('deleteUser', [IndexController::class, 'deleteUser']);
    Route::get('showUserList', [IndexController::class, 'showUserList']);


    Route::post('meeting/add', [MeetingController::class, 'storeMeeting']);
    Route::get('meeting/delete/{id}', [MeetingController::class, 'deleteMeeting']);
    Route::post('meeting/bulk/delete', [MeetingController::class, 'bulkDeleteMeeting']);
    Route::post('meeting/edit', [MeetingController::class, 'editMeeting']);
    Route::get('meeting/all', [MeetingController::class, 'showMeetings']);


    Route::post('customer/add', [CustomerController::class, 'storeCustomer']);
    Route::post('customer/mylist', [CustomerController::class, 'fetchCustomerList']);
    Route::get('customer/delete/{id}', [CustomerController::class, 'deleteCustomer']);
    Route::post('customer/bulk/delete', [CustomerController::class, 'bulkDeleteCustomer']);

    Route::get('customer/all', [CustomerController::class, 'showAllCustomer']);
    Route::post('customer/update', [CustomerController::class, 'updateCustomer']);
    Route::post('customer/login', [CustomerController::class, 'customerLogin']);
    Route::post('customer/change_password', [CustomerController::class, 'changePassword']);
    Route::post('customer/add_point', [CustomerController::class, 'addCustomerPoints']);


    Route::get('reseller/all', [ResellerController::class, 'allReseller']);
    Route::get('reseller/delete/{id}', [ResellerController::class, 'deleteReseller']);
    Route::post('reseller/store', [ResellerController::class, 'storeReseller']);
    Route::post('reseller/update', [ResellerController::class, 'updateReseller']);

    Route::post('notification/add', [NotificationController::class, 'addNotification']);
    Route::post('notification/remove', [NotificationController::class, 'deleteNotification']);
    Route::get('notification/showAll', [NotificationController::class, 'showAll']);

    Route::post('history/reseller_point', [PointHistoryController::class, 'fetchPoints']);
    Route::get('customer/delete/{id}', [CustomerController::class, 'deleteCustomer']);
    Route::get('version/update', [UpdateController::class, 'fetchUpdate']);
    Route::post('version/add', [UpdateController::class, 'addUpdate']);

    Route::post('customer/add_points', [CustomerController::class, 'addCustomerPoints']);
    Route::post('customer/reverse_points', [CustomerController::class, 'reversePoint']);
    Route::get('point/item/{id}', [PointHistoryController::class, 'fetchPointHistoryItem']);

    Route::get('payment/history', [PaymentController::class, 'fetchPaymentHistory']);
    Route::post('customer/payment', [CustomerController::class, 'changePaymentStatus']);
});
