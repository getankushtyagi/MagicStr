<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 
use App\Models\Customer; 
use App\Models\Reseller; 
use App\Models\Meeting; 


class IndexController extends Controller
{ 
    public function index(){

        $data = User::get(); 

        return $data; 
    }
    
    public function getAllData(Request $request){
         $request->validate([
            'appId' => 'required|string',
        ]);
        $userData=User::where('appId',$request->appId)->first();
        
        if($userData->reseller_id!=0){
          $meeting = []; 
          $customer=Customer::where('reseller_id',$userData->id)->get();
          $resellerList = User::where('reseller_id',$userData->id)->get();
           return response()->json([
                'status' => true,
                'meeting'=>$meeting,
                'customer'=>$customer,
                'reseller'=>$resellerList,
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
       return response()->json([
                'status' => true,
                'user'=>$allUser,
                'message'=>'All User List'
            ]); 
        }
        else{
        $allUser = User::where('reseller_id',$user->id)->get(); 
        return response()->json([
                'status' => true,
                'user'=>$allUser,
                'message'=>'All User List'
            ]);    
        }
       
    }
    
}
