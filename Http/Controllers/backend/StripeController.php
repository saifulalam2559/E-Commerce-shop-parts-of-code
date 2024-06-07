<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderMail;


class StripeController extends Controller
{
    
    public function stripeOrder(Request $request) {
        
        
                
         if (!empty(Cart::instance('shopping')->count()) && Auth::check()) {
             
            
             // Start Calculation
             
            $totalproductQty = Cart::instance('shopping')->count();
            $shippingPerQty = 1.85;
            $shippingPrice= $totalproductQty*$shippingPerQty;

            $tax = config('cart.tax')/100;
            $subtotal1 = Cart::subtotal();
            $discount = session()->get('coupon')['value'] ?? 0;
            $newSubTotal1 = $subtotal1-$discount;
            $newTax = $newSubTotal1*$tax;
            
            $finalTotal = $newSubTotal1 *(1+$tax)+$shippingPrice;
            
            // End Calculation
            
            
            
            // Stripe API
            
            \Stripe\Stripe::setApiKey('sk_test_abc');


            $token = $_POST['stripeToken'];

            $charge = \Stripe\Charge::create([
              'amount' => $finalTotal*100,
              'currency' => 'usd',
              'description' => 'Saiful Online Store',
              'source' => $token,
              'metadata' => ['order_id' => uniqid()],
            ]);
            
            
            
            $order_id = Order::insertGetId([
                
                'user_id'=> Auth::id(),
                'first_name'=> $request->first_name,
                'last_name'=> $request->last_name,
                'username'=> $request->username,
                'email'=> $request->email,
                'phone'=> $request->phone,
                'address'=> $request->address,
                'postcode'=> $request->postcode,
                'city'=> $request->city,
                'state'=> $request->state,
                'country'=> $request->country,
                'note'=> $request->note,
                'sfirst_name'=> $request->sfirst_name,
                'slast_name'=> $request->slast_name,
                'saddress'=> $request->saddress,
                'spostcode'=> $request->spostcode,
                'scity'=> $request->scity,
                'sstate'=> $request->sstate,
                'scountry'=> $request->scountry,
                
                'payment_type'=> 'Stripe',
                'payment_method'=> 'Stripe',
                'payment_type'=> $charge->payment_method,
                'transaction_id'=> $charge->balance_transaction,
                'currency'=> $charge->currency,
                'amount'=> $finalTotal,
                'coupon_discount'=> $discount,
                'new_sub_total'=> $newSubTotal1,
                'tax'=> $newTax,
                'shipping_charge'=> $shippingPrice,
                //'order_number'=> 'MAK2021'. mt_rand(100,999),
                'order_number'=> $charge->metadata->order_id,
                'invoice_number'=> 'SA'. mt_rand(10000000,99999999),
                'order_date'=> Carbon::now()->format('d F Y'),
                'order_month'=> Carbon::now()->format('F'),
                'order_year'=> Carbon::now()->format('Y'),
                'status'=> 'Pending',
                'created_at'=> Carbon::now(),
               
                
            ]);

           
            // START SEND EMAIL
            
            $invoice = Order::findOrFail($order_id);    
            
            $data = [
                
                'invoice_number'=> $invoice->invoice_number,
                'amount'=> $finalTotal,
                'first_name'=> $invoice->first_name,
                'last_name'=> $invoice->last_name,
                'email'=> $invoice->email,
                'payment_type'=> 'Stripe',
                'payment_method'=> 'Stripe',
                'phone'=> $invoice->phone,
                
                
                'saddress'=> $invoice->saddress,
                'spostcode'=> $invoice->spostcode,
                'scity'=> $invoice->scity,
                
                
                'amount'=> $finalTotal,
                'coupon_discount'=> $discount,
                'new_sub_total'=> $newSubTotal1,
                'tax'=> $newTax,
                'shipping_charge'=> $shippingPrice,
 
               
                'order_date'=> Carbon::now()->format('d F Y'),
                
            ];
            
          
            
            $carts = Cart::content();
            
            foreach($carts as $cart){
                
                OrderItem::insert([
                    
                    'order_id' => $order_id,
                    'product_id' => $cart->id,
                    'qty' => $cart->qty,
                    'price' => $cart->price,
                    'created_at'=> Carbon::now(),
                    
                ]);
                
            }
            
           
            
            Mail::to($request->email)->cc(['saifulalam2559@gmail.com'])->send(new OrderMail($data , $carts));
            
            
            if(Session::has('coupon')){
                
                Session::forget('coupon');
            }
            
            
             Cart::destroy();
             
              return redirect()->route('orderSuccess')->with('success','Your Order Placed Successfully!');
              

         } else {
             
             return redirect()->route('shop')->with('success','Cart is empty ! Please select some product!');
             
         }
        
           

            
            
        
    }
    
    
}
