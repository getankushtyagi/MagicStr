<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Update; 
class UpdateController extends Controller
{


    public function addUpdate(Request $request){

        $update = new Update; 
        $update->title = $request->title; 
        $update->description = $request->description; 
        $update->version = $request->version; 
        $update->code = $request->code; 
        $update->download_link = $request->download_link;
        $update->save(); 

        
        return response()->json([
            'status' => true,
            'message' => 'Updated added successfully',
            'data' => $update
        ]); 
    }
    
    
    public function fetchUpdate(){

         $update = Update::all();

        
        return response()->json([
            'status' => true,
            'message' => 'Data Fatch successfully',
            'data' => $update
        ]); 
    }
    
}
