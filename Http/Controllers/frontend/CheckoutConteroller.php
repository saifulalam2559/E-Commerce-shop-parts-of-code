<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Cart;





class CheckoutConteroller extends Controller {
    

    
    public function checkout1() {
        
         if (!empty(Cart::instance('shopping')->count())) {
             

             
             $user = Auth::user();
             return view('frontend.pages.checkout.checkout1', compact('user'));
             
         } else {
             
            return redirect()->route('shop')->with('success','Cart is empty ! Please select some product!');
             
         }
             
        
        
    }
    
    
    
    
    public function checkout1Store(Request $request) {
        
            $data = array();
            $data['first_name']= $request->first_name;
            $data['last_name']= $request->last_name;
            $data['username']= $request->username;
            $data['email']= $request->email;
            $data['phone']= $request->phone;
            $data['address']= $request->address;
            $data['postcode']= $request->postcode;
            $data['city']= $request->city;
            $data['state']= $request->state;
            $data['country']= $request->country;
            $data['note']= $request->note;
            $data['sfirst_name']= $request->sfirst_name;
            $data['slast_name']= $request->slast_name;
            $data['saddress']= $request->saddress;
            $data['spostcode']= $request->spostcode;
            $data['scity']= $request->scity;
            $data['sstate']= $request->sstate;
            $data['scountry']= $request->scountry;
            
            if( $request->payment_method == 'stripe'){
                
                        if (!empty(Cart::instance('shopping')->count()) && Auth::check()) {
                   
                            return view('frontend.payment.stripe', compact('data'));

                         } else {

                             return redirect()->route('shop')->with('success','Cart is empty ! Please select some product!');

                         }
              
            } elseif($request->payment_method == 'paypal'){
                
                       if (!empty(Cart::instance('shopping')->count()) && Auth::check()) {
                   
                     
                            $gateway = new \Braintree\Gateway([
                            'environment' => config('services.braintree.environment'),
                            'merchantId' => config('services.braintree.merchantId'),
                            'publicKey' => config('services.braintree.publicKey'),
                            'privateKey' => config('services.braintree.privateKey')
                            ]);


                             $paypalToken = $gateway->ClientToken()->generate();

                             return view('frontend.payment.paypal', compact('data','paypalToken'));
                    
                    
                    

                         } else {

                             return redirect()->route('shop')->with('success','Cart is empty ! Please select some product!');

                         }
                 
                 
            }else{
                
                 return redirect()->route('shop')->with('success','ERROR !!! There is something problem!');
            }
            
        
    }
    
    
    
    public function orderSuccess(Request $request) {
             
        
        return view('frontend.pages.checkout.payment_success');
        
    }
    
    
    
}
