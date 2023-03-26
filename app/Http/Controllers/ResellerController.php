<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reseller;
use Illuminate\Support\Facades\Crypt;

class ResellerController extends Controller
{
    public function allReseller(){

        $data = Reseller::get(); 
        if($data){
            return response()->json([
                'status' => true,
                'data' => $data,
                'message'=>'All Resellers'
            ]);
        }
    }

    public function deleteReseller($id){

        $delete = Reseller::where('id',$id)->delete();
        if($delete){
            return response()->json([
                'status' => true,
                 'message'=>'Reseller Deleted Successfully'
            ]);
        }
    }

    public function storeReseller(Request $request){

        $image = $request->file('image'); 
        if($image){
            $img = $image->getClientOriginalName(); 
            $image->move('reseller',$image); 
        }else{
            $image= null ;
        
        }
        $data = new Reseller; 
        $data->name = $request->name; 
        $data->email = $request->email; 
        $data->mobile = $request->mobile; 
        $data->password = $request->password; 
        $data->points = $request->points; 
        $data->image =isset($img) ? $img : 'no image found'; 
        $data->save(); 

        if($data){
            return response()->json([
                'status' => true,
                'data' => $data,
                'message'=>'New Reseller Created Successfully'
            ]);
        }
    }
        public function updateReseller(Request $request){

        $image = $request->file('image'); 
        if($image){
            $img = $image->getClientOriginalName(); 
            $image->move('reseller',$image); 
        }else{
            $image= null ;       
        }

        $id = $request->reseller_id; 

        $update = Reseller::where('id',$id)->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'mobile'=>$request->mobile,
            //remove point
                // 'points'=>$request->points,
            'password'=>Crypt::encrypt($request->password),  
            'image'=>isset($img) ? $img : $request->reseller_image,
        ]); 
        if($update){
            return response()->json([
                'status' => true,
                'message'=>'Reseller Updated Successfully'
            ]);
        }


    }
}
