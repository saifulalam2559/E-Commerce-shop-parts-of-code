<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;
use App\Models\OrderItem;
use PDF;





class FrontUserController extends Controller
{
    
    
             
    
    
    public function userDashboard() {
        
         $user = Auth::user();
        // dd($user);
        
         return view('frontend.user.dashboard', compact('user'));
        
    }
    
    

    
    
        public function userAddress() {
        
         $user = Auth::user();
        // dd($user);
        
         return view('frontend.user.address', compact('user'));
        
    }
    
    
            public function useraccountDetail() {
        
         $user = Auth::user();
        // dd($user);
        
         return view('frontend.user.account_detail', compact('user'));
        
    }
    
             
    
    public function userBillingAddress(Request $request,$id) {
        
//        $all= $request->all();
//        $all = json_decode(json_encode($all));
//        echo '<pre>';         print_r($all) ; die; 
        
        

        
        $user = User::where('id',$id)->update([
            
            'first_name' =>$request->first_name,
            'last_name' =>$request->last_name,
            'address' =>$request->address,
            'postcode' =>$request->postcode,
            'city' =>$request->city,
            'state' =>$request->state,
            'country' =>$request->country,
            'phone' =>$request->phone,
            
            
        ]);
        
        if($user){
            
            return back()->with('success','Billing address updated successfully!');
            
        } else {
            
            return back()->with('error','There is something wrong!');
            
        }
    
        
    }
    
    
    
    
       public function userShippingAddress(Request $request,$id) {
        
//        $all= $request->all();
//        $all = json_decode(json_encode($all));
//        echo '<pre>';         print_r($all) ; die; 
        
        
        $user = User::where('id',$id)->update([
            
            'sfirst_name' =>$request->sfirst_name,
            'slast_name' =>$request->slast_name,
            'saddress' =>$request->saddress,
            'spostcode' =>$request->spostcode,
            'scity' =>$request->scity,
            'sstate' =>$request->sstate,
            'scountry' =>$request->scountry,
            'phone' =>$request->phone,
            
            
        ]);
        
        
        
        if($user){
            
            return back()->with('success','Shipping address updated successfully!');
            
        } else {
            
            return back()->with('error','There is something wrong!');
            
        }
    
        
    }
     
    
    
    
    public function userUpdateAccount(Request $request,$id){
        
//        $all= $request->all();
//        $all = json_decode(json_encode($all));
//        echo '<pre>';         print_r($all) ; die; 
        
                
          $this->validate($request, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'username' => 'required|string', 
            'phone' => 'required', 
            'newpassword' => 'nullable|min:4',
            
        ]);
             
        
        
        $hashpassword = Auth::user()->password;
        //return $hashpassword ;
        
        if($request->oldpassword == null && $request->newpassword == null){
            
             User::where('id',$id)->update([
            
            'first_name' =>$request->first_name,
            'last_name' =>$request->last_name,
            'username' =>$request->username, 
            'phone' =>$request->phone,
            
            
        ]);
             
             return back()->with('success','Account successfully updated!');    
             
        } else {
            
            
            if(\Hash::check( $request->oldpassword,$hashpassword)){   // 2nd if condition
                
                
                if(!\Hash::check( $request->newpassword,$hashpassword)){  // 3nd if condition
                
                
                    
                                 User::where('id',$id)->update([
            
                                'first_name' =>$request->first_name,
                                'last_name' =>$request->last_name,
                                'username' =>$request->username,
                                'password' =>Hash::make($request->newpassword),   
                                'phone' =>$request->phone,


                            ]);
                                 
                         return back()->with('success','Account successfully updated!');         
                
            } else {
                
                return back()->with('error','New password can not be same with old password!');
                
            } // 3nd if condition
                
                
                
            } else {
                
                return back()->with('error','Old password does not match!');
                
            } // 2nd if condition
            
            
            
            
        }  // 1st if condition
        
        
        

    
        
        
    }
    
    
    
    
    public function myOrder() {
        
         $user = Auth::user();
        // dd($user);
        
         $orders = Order::where('user_id',Auth::id())->orderBy('id','DESC')->get();  // We can get only specific user using where('user_id',Auth::id())
         return view('frontend.user.order', compact('user','orders'));
        
    }
    
    
    public function orderDetails($order_id) {
        
         
        $order = Order::where(['id'=>$order_id,'user_id'=>Auth::id()])->first();
        $orderItem = OrderItem::with('product')->where('order_id',$order_id)->orderBy('id','DESC')->get();
        
        return view('frontend.user.order_details', compact('order','orderItem'));
        
    }
    
    
    
    public function invoiceDownload($order_id) {
        
        
        $order = Order::where(['id'=>$order_id,'user_id'=>Auth::id()])->first();
        $orderItem = OrderItem::with('product')->where('order_id',$order_id)->orderBy('id','DESC')->get();
        
        //return view('frontend.user.order_invoice_download', compact('order','orderItem'));
        
        $pdf = PDF::loadView('frontend.user.order_invoice_download', compact('order','orderItem'));
        return $pdf->download('invoice.pdf');
        
    }
    
    
    
    
    
}
