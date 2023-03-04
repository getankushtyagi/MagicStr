<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Meeting; 


class MeetingController extends Controller
{
    public function storeMeeting(Request $request){
      
        $user = Meeting::where('name',$request->name)->first();
        if($user){
            return response()->json([
                'status' => false,
                'message'=>'Meeting already exists'
            ]); 
        }
        $customer = []; 
        $name[] = $request->customer_name ; 
        foreach($name as $nam){
          $customer[] = $nam ; 
        }
        $data = new Meeting; 
        $data->name = $request->name; 
        $data->meeting_id = $request->meeting_id ; 
        $data->passcode = $request->passcode; 
        $data->sdk_key = $request->sdk_key; 
        $data->sdk_secret = $request->sdk_secret; 
        $data->jdk_token = $request->jdk_token; 
        $data->show = $request->show; 
         $data->pattern = $request->pattern; 
        $data->customer_name = implode(',',$customer);
        $data->save(); 

        if($data){
            return response()->json([
                'status' => true,
                'data' => $data,
                'message'=>'New Meeting Created Successfully'
            ]);
        }else{
             return response()->json([
                'status' => false,
                'data' => $data,
                'message'=>'Server failed'
            ]);
        }
    }

    public function deleteMeeting($id){

        $delete = Meeting::where('id',$id)->delete(); 
        if($delete){
            return response()->json([
                'status' => true,
                'message'=>'Meeting Deleted Successfully'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message'=>'Server failed'
            ]);
        }
    }

    public function bulkDeleteMeeting(Request $request){

        $id[]  = $request->id ; 

        foreach($id as $i){

            Meeting::where('id',$i)->delete(); 

        }
        return response()->json([
            'status' => true,
            'message'=>'Meeting Deleted Successfully'
        ]);

    }
    
        public function editMeeting(Request $request){

        $customer = []; 
        $name[] = $request->customer_name; 

        $update = Meeting::where('name',$request->name)->update([
            'name'=>$request->name,
            'meeting_id'=>$request->meeting_id,
            'passcode'=>$request->passcode,
            'show'=>$request->show,
            'customer_name'=>implode(',',$name),
            'sdk_key'=>$request->sdk_key,
            'sdk_secret'=>$request->sdk_secret,
            'jdk_token'=>$request->jdk_token
            ]); 

            if($update){
                return response()->json([
                    'status' => true,
                    'message'=>'Meeting Updated Successfully'
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'message'=>'Server failed'
                ]);
            }
    }
    
      public function showMeetings(){

        $data = Meeting::all(); 

        if($data){
            return response()->json([
                'status' => true,
                'data' => $data,
                'message'=>'All Meetings'
            ]);
        }else{
              return response()->json([
                'status' => false,
                'data' => $data,
                'message'=>'Server failed'
            ]);
        }

    }
}
