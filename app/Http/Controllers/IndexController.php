<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 
use App\Models\Customer; 
use App\Models\Reseller; 
use App\Models\Meeting;
use Illuminate\Support\Facades\Crypt;

class IndexController extends Controller
{ 
    public function index(){

        $data = User::get(); 

        return $data; 
    }
    
    public function getAllData(Request $request){
        try {
            $request->validate([
                'appId' => 'required|string',
            ]);
            $userData=User::where('appId',$request->appId)->first();
            
            if($userData->reseller_id!=0){
              $meeting = []; 
              $customer=Customer::where('reseller_id',$userData->id)->get();
                $cus_arr=[];
              foreach($customer as $key=>$val){
                $cus_arr[$key]['id'] = $val['id'];
                $cus_arr[$key]['appId'] = $val['appId'];
                $cus_arr[$key]['customer_type'] = $val['customer_type'];
                $cus_arr[$key]['reseller_id'] = $val['reseller_id'];
                $cus_arr[$key]['name'] = $val['name'];
                $cus_arr[$key]['username'] = $val['username'];
                $cus_arr[$key]['password'] = Crypt::decrypt($val['password'])??$val['password'];
                $cus_arr[$key]['point_reverse'] = $val['point_reverse'];
                $cus_arr[$key]['pstatus'] = $val['pstatus'];
                $cus_arr[$key]['created_at'] = $val['created_at'];
                $cus_arr[$key]['updated_at'] = $val['updated_at'];
                $cus_arr[$key]['ios_point_expiry'] = $val['ios_point_expiry'];
                $cus_arr[$key]['android_point_expiry'] = $val['android_point_expiry'];
              }
            //   dump($cus_arr);
              $resellerList = User::where('reseller_id',$userData->id)->get();
            //   dump($resellerList);
              $reseller_arr=[];
              foreach($resellerList as $key=>$val){
                $reseller_arr[$key]['id'] = $val['id'];
                $reseller_arr[$key]['appId'] = $val['appId'];
                $reseller_arr[$key]['name'] = $val['name'];
                $reseller_arr[$key]['role_id'] = $val['role_id'];
                $reseller_arr[$key]['reseller_id'] = $val['reseller_id'];
                $reseller_arr[$key]['user_name'] = $val['user_name'];
                $reseller_arr[$key]['mobile'] = $val['mobile'];
                $reseller_arr[$key]['customer'] = $val['customer'];
                $reseller_arr[$key]['image_index'] = $val['image_index'];
                $reseller_arr[$key]['password'] = Crypt::decrypt($val['password'])??$val['password'];
                $reseller_arr[$key]['created_at'] = $val['created_at'];
                $reseller_arr[$key]['updated_at'] = $val['updated_at'];
                $reseller_arr[$key]['ios_point'] = $val['ios_point'];
                $reseller_arr[$key]['android_point'] = $val['android_point'];
              }
               return response()->json([
                    'status' => true,
                    'meeting'=>$meeting,
                    'customer'=>$cus_arr,
                    'reseller'=>$reseller_arr,
                    'message'=>'All Details'
                ]);
            }else{
              $meeting = Meeting::all(); 
              $customer=Customer::all();
              $resellerList = User::all(); 
               return response()->json([
                    'status' => true,
                    'meeting'=>$meeting,
                    'customer'=>$customer,
                    'reseller'=>$resellerList,
                    'message'=>'All Details'
                ]);    
            }
        } catch (\Exception $th) {
            dd($th);
        }
        
    }

    public function updateUser(Request $request){
        $id = $request->appId; 
        $update = User::where('appId',$id)->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'mobile'=>$request->mobile,
            'points'=>$request->points,
            'role_id'=>$request->role_id,
        ]); 
        if($update){
             $data = User::where('appId',$id)->first();
            return response()->json([
                'status' => true,
                'data'=>$data,
                'message'=>'Reseller Updated Successfully'
            ]);
        }
    }   
    
    
    public function deleteUser(Request $request){
        $delete = User::where('appId',$request->appId)->delete(); 
        if($delete){
            return response()->json([
                'status' => true,
                'message'=>'Reseller Deleted Successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message'=>'Server failed'
            ]);
        }
    }    
    public function getAllUser(Request $request){
         $request->validate([
            'appId' => 'required|string',
        ]);
        $user = User::where('appId',$request->appId)->first(); 
        if($user->reseller_id==0){
        $allUser = User::all(); 

        $reseller_arr=[];
        foreach($allUser as $key=>$val){
          $reseller_arr[$key]['id'] = $val['id'];
          $reseller_arr[$key]['appId'] = $val['appId'];
          $reseller_arr[$key]['name'] = $val['name'];
          $reseller_arr[$key]['role_id'] = $val['role_id'];
          $reseller_arr[$key]['reseller_id'] = $val['reseller_id'];
          $reseller_arr[$key]['user_name'] = $val['user_name'];
          $reseller_arr[$key]['mobile'] = $val['mobile'];
          $reseller_arr[$key]['customer'] = $val['customer'];
          $reseller_arr[$key]['image_index'] = $val['image_index'];
          $reseller_arr[$key]['password'] = Crypt::decrypt($val['password'])??$val['password'];
          $reseller_arr[$key]['created_at'] = $val['created_at'];
          $reseller_arr[$key]['updated_at'] = $val['updated_at'];
          $reseller_arr[$key]['ios_point'] = $val['ios_point'];
          $reseller_arr[$key]['android_point'] = $val['android_point'];
        }
       return response()->json([
                'status' => true,
                'user'=>$reseller_arr,
                'message'=>'All User List'
            ]); 
        }
        else{
        $allUser = User::where('reseller_id',$user->id)->get(); 
        $reseller_arr=[];
        foreach($allUser as $key=>$val){
          $reseller_arr[$key]['id'] = $val['id'];
          $reseller_arr[$key]['appId'] = $val['appId'];
          $reseller_arr[$key]['name'] = $val['name'];
          $reseller_arr[$key]['role_id'] = $val['role_id'];
          $reseller_arr[$key]['reseller_id'] = $val['reseller_id'];
          $reseller_arr[$key]['user_name'] = $val['user_name'];
          $reseller_arr[$key]['mobile'] = $val['mobile'];
          $reseller_arr[$key]['customer'] = $val['customer'];
          $reseller_arr[$key]['image_index'] = $val['image_index'];
          $reseller_arr[$key]['password'] = Crypt::decrypt($val['password'])??$val['password'];
          $reseller_arr[$key]['created_at'] = $val['created_at'];
          $reseller_arr[$key]['updated_at'] = $val['updated_at'];
          $reseller_arr[$key]['ios_point'] = $val['ios_point'];
          $reseller_arr[$key]['android_point'] = $val['android_point'];
        }
        return response()->json([
                'status' => true,
                'user'=>$allUser,
                'message'=>'All User List'
            ]);    
        }
       
    }
    
}
