<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;

class PaymentController extends Controller
{
    
    public function addPaymentStatus(string $customerId, string $resellerId, string $status, string $remarks){
        
        $data = Payment::where('customer_id',$customerId)->first();
        if($data){
          $update = Payment::where('customer_id',$customerId)->update([
            'reseller_id'=>$resellerId,
            'status'=>$status,
            'remarks'=>$remarks,
        ]);
         return $data;
        }else{
        $data = new Payment; 
        $data->status = $status; 
        $data->customer_id = $customerId; 
        $data->reseller_id = $resellerId; 
        $data->status = $status; 
        $data->remarks = $remarks; 
        $data->save(); 
        if($data){
            return $data;
        }else{
            return null;
            
        }    
        }
    }
    
    public function fetchPaymentHistory(){

        $customers = Payment::all(); 
        
        return response()->json([
                'status' => true,
                'data' => $customers,
                'message'=>'Payment History'
            ]);
    }    
}
