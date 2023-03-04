<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification; 

class NotificationController extends Controller
{
    //
    
    public function addNotification(Request $request){

        $data = new Notification; 
        $data->type = $request->type; 
        $data->msg = $request->msg; 
        $data->link = $request->link; 
        $data->app_link = $request->app_link; 
        $data->image_link = $request->image_link;
        $data->from = $request->from;
        $data->to = $request->to;
        $response= $data->save(); 
        if($response){
        return response()->json([
            'status' => true,
            'message' => 'Notification created successfully',
        ]);
        }else{
             return response()->json([
            'status' => false,
            'message' => 'Server failed',
        ]);
        }
        // $balance = $resellerUser.points - $request->points;
        
        // $pointUpdate=  $this->updatePoints($resellerUser->id, $balance);
    }
    
      public function showAll(){

        $data = Notification::all(); 

        if($data){
            return response()->json([
                'status' => true,
                'data' => $data,
                'message'=>'All Notification'
            ]);
        }else{
              return response()->json([
                'status' => false,
                'data' => $data,
                'message'=>'Server failed'
            ]);
        }
    } 
    public function deleteNotification(Request $request){

        $delete = Notification::where('id',$request->id)->delete(); 
        if($delete){
            return response()->json([
                'status' => true,
                'message'=>'Notification Deleted Successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message'=>'Server failed'
            ]);
        }
    }    
}
