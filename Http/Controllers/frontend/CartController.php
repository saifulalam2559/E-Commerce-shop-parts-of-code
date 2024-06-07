<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Coupons;
use Illuminate\Support\Facades\Session;
use Cart;




class CartController extends Controller
{
    
    
    public function cartStore(Request $request){
        
        
        $product_qty = $request->input('product_qty'); 
        $product_id = $request->input('product_id');    
        $product = Product::getProductByCart($product_id);
        $price = $product[0]['offer_price'];
        
        $cart_array=[];
        
        foreach(Cart::instance('shopping') as $product){
            
            $cart_array[]=$product->id;
            
        }
        
        
        $result=Cart::instance('shopping')->add($product_id,$product[0]['title'],$product_qty,$price)->associate('App\Models\Product');
        
        if($result){
            
            $response['status']=true;
            $response['product_id']=$product_id;
            $response['total']=Cart::subtotal();
            $response['cart_count']= Cart::instance('shopping')->count();
            $response['message']= 'Product added sccessfully to cart!!';
            
        }
        
        if($request->ajax()){  
            
            $header = view('frontend.layouts.main_header')->render();
            $response['header']= $header;
            
        }
        
        return json_encode($response);
        
        
        
    }
    
    
    
    public function cartDelete(Request $request) {
        
        
        $id = $request->input('cart_id');
        Cart::instance('shopping')->remove($id);
        
        
            $response['status']= true;
            $response['total']=Cart::subtotal();
            $response['cart_count']= Cart::instance('shopping')->count();
            $response['message']= 'Cart Deleted Successfully';
        
            if($request->ajax()){
            
            $header = view('frontend.layouts.main_header')->render();
            $response['header']= $header;
            $cart_list = view('frontend.layouts._cart_list')->render();
            $response['cart_list']= $cart_list;

            
        }
        
         return json_encode($response);
     
        
    }
    
   
    // Cart Listing View
    public function cart() {
       
        
          return view('frontend.pages.cart.index');
        
    }
    
    
    public function cartUpdate(Request $request) {
        
        $this->validate($request, [
            
            'product_qty'=>'required|numeric',
            
        ]);
        
        $rowId = $request->input('rowId');
        $request_quantity = $request->input('product_qty');
        $productQuantity = $request->input('productQuantity');
        
        if($request_quantity>$productQuantity ){
            
            $message = 'Sorry !! We do not have enough product in our stock!';
            $response['status'] = false;
            
            
        } elseif($request_quantity<=0){
            
            $message = 'You can not add less than 1 quantity!';
            $response['status'] = false;
            
        } else {
            
            Cart::instance('shopping')->update($rowId,$request_quantity);
            $message = 'Product Quantity and Price updated successfully!';
            $response['status'] = true;
            $response['total']=Cart::subtotal();
            $response['cart_count']= Cart::instance('shopping')->count();
            
        }
        
         if($request->ajax()){
            
            $header = view('frontend.layouts.main_header')->render();
            $response['header']= $header;
            $cart_list = view('frontend.layouts._cart_list')->render();
            $response['cart_list']= $cart_list;
            $response['message']= $message;

            
        }
        
        
          return $response;
        
        
        
    }
    
   
    // Coupon Apply
    
    public function couponApply(Request $request) {
        
        $coupon = Coupons::where('code',$request->input('code'))->where('status','active')->first();
        
        if(!$coupon){
            
            return back()->with('error','Invalid coupon code !');
            
        }
        
        if($coupon){
            
           $total_price = Cart::instance('shopping')->subtotal();
           session()->put('coupon',[
               
               'id' => $coupon->id,
               'code' => $coupon->code,
               'value' => $coupon->discount($total_price),   
               
           ]);
           
          
           return back()->with('success','Coupon Applied successfully !');
           
        }
        
        
    }
    
    
    public function couponDelete(Request $request) {
        
        $request->session()->forget('coupon');
        return redirect()->route('cart')->with('success','Coupon has been removed!');
        
    }
    
    
    
    
}
