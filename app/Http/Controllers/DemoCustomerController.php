<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\DemoCustomer;

class DemoCustomerController extends Controller
{
        public function storeCustomer(Request $request){
         $user = Customer::where('username',$request->username)->first();
        if($user){
            return response()->json([
                'status' => false,
                'message'=>'User Already Registered'
            ]); 
        }
        $data = new Customer; 
        $data->name = $request->name; 
        $data->customer_type = $request->customer_type; 
        $data->username = $request->username; 
        $data->password = $request->password; 
        $data->reseller_id = $request->reseller_id; 
        $data->active_date = $request->active_date; 
        $data->plan_expiry_date = $request->plan_expiry_date; 
        $data->save(); 
        if($data){
            return response()->json([
                'status' => true,
                'data' => $data,
                'message'=>'Customer Created Successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message'=>'Something went successfully'
            ]);
            
        }
    }
}
