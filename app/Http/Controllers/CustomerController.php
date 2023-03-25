<?php

namespace App\Http\Controllers;

use \Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;
use App\Models\Meeting;
use App\Models\PointHistory;
use App\Models\User;
use App\Models\Payment;

class CustomerController extends Controller
{

    protected $pointHistoryController;
    protected $userController;
    protected $paymentController;
    public function __construct(PointHistoryController $pointHistoryController, AuthController $userController, PaymentController $paymentController)
    {
        $this->pointHistoryController = $pointHistoryController;
        $this->userController = $userController;
        $this->paymentController = $paymentController;
    }

    public function storeCustomer(Request $request)
    {
        $user = Customer::where('username', $request->username)->first();
        if ($user) {
            return response()->json([
                'status' => false,
                'message' => 'User Already Registered'
            ]);
        }

        $data = new Customer;
        $data->appId = 'app_' . uniqid();
        $data->name = $request->name;
        $data->customer_type = $request->customer_type;
        $data->username = $request->username;
        $data->password = $request->password;
        $data->reseller_id = $request->reseller_id;
        $data->active_date = $request->active_date;
        $dat=$request->active_date;
        //expiry date is manage from backend
        $addexpiredate=strtotime($dat.'+30 days');
        $addexpiredateformat=Date('Y-m-d h:i:s',$addexpiredate);
        $data->plan_expiry_date = $addexpiredateformat;
        $data->save();

        $resellerName =  User::where('id', $request->reseller_id)->first();
        $resellerName->end_date = $request->reseller_end_date;
        $resellerName->save();


        $remark = "$request->points point added by $resellerName->name to $request->username";

        //store data in point_history table to manage all records
        $result = $this->pointHistoryController->addPoints($request->reseller_id, $data->id, $request->points, $remark);

        $UpdateDetails = Customer::where('id', '=',  $data->id)->first();
        $UpdateDetails->point_reverse = $result->id;//point history row id 
        $UpdateDetails->save();

        if ($data && $request->customer_type == 2 || $data && $request->customer_type == 3) {
            $meeting = Meeting::all();
            return response()->json([
                'status' => true,
                'data' => $data,
                'pointh' => $result,
                'policy' => 'https://www.nxtlevel.live/privacy-policy',
                'meeting' => $meeting,
                'message' => 'New Customer Created Successfully'
            ]);
        } else if ($request->customer_type == 1) {
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'New Customer Created Successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Data not found'
            ]);
        }
    }

    public function deleteCustomer($id)
    {
        $delete = Customer::where('id', $id)->delete();
        if ($delete) {
            return response()->json([
                'status' => true,
                'message' => 'Customer Deleted Successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Request failed'
            ]);
        }
    }

    public function addCustomerPoints(Request $request)
    {
        $id = $request->appId;
        $rid = $request->rid;
        $point = $request->points;
        $resellerEndDate = $request->reseller_end_date;
        $userEndDate = $request->user_end_date;
        $point_type = $request->point_type??"ios";


        $user = Customer::where('appId', $id)->first();

        $resellerUser = User::where('id', $rid)->first();
        $resellerUser->end_date = $resellerEndDate;
        $resellerUser->save();


        $remark = "$point point added by $resellerUser->name to $user->name";
        $result = $this->pointHistoryController->addPoints($resellerUser->id, $user->id, $request->points, $remark,$point_type);
        $user->point_reverse = $result->id;
        $user->plan_expiry_date = $userEndDate;
        $user->save();
        if ($result) {
            return response()->json([
                'status' => true,
                'message' => 'Pointed Added Successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Request failed'
            ]);
        }
    }

    public function changePaymentStatus(Request $request)
    {
        $cid = $request->customer_id;
        $rid = $request->reseller_id;
        $status = $request->status;
        $remarks = $request->remarks;

        $result = $this->paymentController->addPaymentStatus($cid, $rid, $status, $remarks);

        $update = Customer::where('id', $cid)->update([
            'pstatus' => $cid
        ]);

        if ($update) {
            return response()->json([
                'status' => true,
                'message' => 'Status changed'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Request failed'
            ]);
        }
    }

    public function reversePoint(Request $request)
    {

        $id = $request->appId;
        $rid = $request->rid;
        $point = $request->points;
        $resellerEndDate = $request->reseller_end_date;
        $userEndDate = $request->user_end_date;

        $user = Customer::where('appId', $id)->first();

        $resellerUser = User::where('id', $rid)->first();
        $resellerUser->end_date = $resellerEndDate;
        $resellerUser->save();

        $remark = "$point point reversed by $resellerUser->name from $user->name";
        $result = $this->pointHistoryController->addPoints($resellerUser->id, $user->id, $request->points, $remark);

        $user->point_reverse = "0";
        $user->plan_expiry_date = $userEndDate;
        $user->save();
        if ($result) {
            return response()->json([
                'status' => true,
                'message' => 'Pointed Reversed Successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Request failed'
            ]);
        }
    }

    public function bulkDeleteCustomer(Request $request)
    {

        $id[] = $request->customer_id;

        foreach ($id as $c_id) {
            Customer::where('id', $c_id)->delete();
        }

        return response()->json([
            'status' => true,
            'message' => 'Customer Deleted Successfully'
        ]);
    }

    public function customerLogin(Request $request)
    {
        $name = $request->username;
        $password = $request->password;
        $platform = $request->platform??"ios";

        $user = Customer::where('username', $name)->first();
        if ($user->password == $password) {
            //customer type 2-> paid 3-> demo 
            if ($user->customer_type == 2 || $user->customer_type == 3) {
                $meeting = Meeting::all();
                return response()->json([
                    'success' => true,
                    'message' => 'Login Successfully',
                    'user' => $user,
                    'policy' => 'https://www.nxtlevel.live/privacy-policy',
                    'meeting' => $meeting
                ], 200);
            } else if ($user->customer_type == 1) { //customer type 1-> apple user
                $date = Carbon::now();
                $meeting1 = [
                    "id" => "32",
                    "name" => "Rakesh",
                    "session" => "morning",
                    "duration" => "1 hours",
                    "subject" => "Mathematics",
                    "period" => "8-12",
                    "start_date" => $date->addDays(2),
                ];
                $meeting2 = [
                    "id" => "33",
                    "name" => "Mohit",
                    "session" => "evening",
                    "duration" => "1 hours",
                    "subject" => "Science",
                    "period" => "8-12",
                    "start_date" => $date->addDays(2),
                ];
                $meetings = [$meeting1, $meeting2];
                return response()->json([
                    'status' => true,
                    'data' => $user,
                    'policy' => 'https://www.nxtlevel.live/privacy-policy',
                    'meetings' => $meetings,
                    'message' => 'Login Successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Login  failed',
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Login  fail!',
            ], 400);
        }
    }


    public function showAllCustomer()
    {

        $customers = Customer::all();

        return response()->json([
            'status' => true,
            'data' => $customers,
            'message' => 'All Customers'
        ]);
    }

    public function fetchCustomerList(Request $request)
    {
        if ($request->access == "0") {
            $data = Customer::all();
        } else {
            $data = Customer::where('reseller_id', $request->id)->get();
        }


        if ($data) {
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'All Customers'
            ]);
        }
    }

    public function updateCustomer(Request $request)
    {
        $id = $request->id;
        $update = Customer::where('id', $id)->update([
            'name' => $request->name,
            'username' => $request->username,
            'customer_type' => $request->customer_type,
            'plan_expiry_date' => $request->plan_expiry_date
        ]);

        if ($update) {
            return response()->json([
                'status' => true,
                'message' => 'Customer Updated Successfully'
            ]);
        }
    }

    public function changePassword(Request $request)
    {
        $id = $request->id;
        $update = Customer::where('id', $id)->update([
            'password' => $request->password,
        ]);

        if ($update) {
            return response()->json([
                'status' => true,
                'message' => 'Password Updated Successfully'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }
}
