<?php

namespace App\Http\Controllers;
use App\Models\PointHistory;
use Illuminate\Http\Request;


class PointHistoryController extends Controller
{
    
 
 public function addPoints(string $rId, string $cId, string $point, string $remarks){
        $data = new PointHistory; 
        $data->reseller_id = $rId; 
        $data->customer_id = $cId; 
        $data->points = $point; 
        $data->remarks = $remarks; 
        $data->save(); 
        if($data){
            return $data;
        }else{
            return null;
            
        }
    }
    
 
 public function addCustomerPoints(Request $request){
        $data = new PointHistory; 
        $data->reseller_id = $request->id; 
        $data->customer_id = $request->cid; 
        $data->points = $request->point; 
        $data->remarks = $request->remarks; 
        $data->save(); 
        if($data){
            return response()->json([
                'status' => true,
                'data' => $data,
                'message'=>'Points added Successfully'
            ]);  
        }else{
            return null;
            
        }
    }
    
 public function fetchPointHistoryItem($id){
        $data = PointHistory::where('id',$id)->first();
        if($data){
            return response()->json([
                'status' => true,
                'data' => $data,
                'message'=>'Data Fetch Successfully'
            ]);  
        }else{
            return null;
        }
    }    
    
    
        
 public function fetchPoints(Request $request){
     
     if($request->has('reseller_id')){
     $data = PointHistory::where('reseller_id',$request->reseller_id)->get(); 
        if($data){
            return response()->json([
                'status' => true,
                'data' => $data,
                'message'=>'Points Fetch Successfully'
            ]);    
     }else{
            return response()->json([
                'status' => false,
                'message'=>'Data not found'
            ]);
            
        }
     }else if($request->has('customer_id')){
        $data = PointHistory::where('customer_id',$request->customer_id)->get(); 
        if($data){
            return response()->json([
                'status' => true,
                'data' => $data,
                'message'=>'Points Fetch Successfully'
            ]);  }
         else{
            return response()->json([
                'status' => false,
                'message'=>'Data not found'
            ]);
            
        }
     
        }else{
            return response()->json([
                'status' => false,
                'message'=>'Data not found'
            ]);
            
        }
    }    
    
}
