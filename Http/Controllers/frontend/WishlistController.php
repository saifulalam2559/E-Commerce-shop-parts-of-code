<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Product;
use Cart;



class WishlistController extends Controller
{
    
    public function wishlist() {
        
        $banners=Banner::where(['status'=>'active','condition'=>'banner'])->get();
        return view('frontend.pages.wishlist', compact('banners'));
        
        
    }
    
    
    public function wishlistStore(Request $request) {

        $product_id = $request->input('product_id');    
        $product_qty = $request->input('product_qty'); 
        $product = Product::getProductByCart($product_id); 
        $price = $product[0]['offer_price'];
   
        $wishlist_array=[];
        
        foreach(Cart::instance('wishlist') as $product){
            
        $wishlist_array[]=$product->id;
            
        }
        
        if(in_array($product_id,$wishlist_array)){
            
             
             $response['present']= true;  
             $response['message']= 'Product is already in your Wishlist!!';
             
        } else {
            
             $result=Cart::instance('wishlist')->add($product_id,$product[0]['title'],$product_qty,$price)->associate('App\Models\Product');
             
             if($result){
            
            $response['status']=true;  
            $response['wishlist_count']= Cart::instance('wishlist')->count();
            $response['message']= 'Product added sccessfully to Wishlist!!';
            
        }
             
        }
        
         if($request->ajax()){  
            
            $header = view('frontend.layouts.main_header')->render();
            $response['header']= $header;
            
        }
        
         return json_encode($response);
        
        
    }
    
    
    public function wishlistMoveToCart(Request $request) {
        
         $product =Cart::instance('wishlist')->get($request->input('rowId'));   
         Cart::instance('wishlist')->remove($request->input('rowId'));
         $result = Cart::instance('shopping')->add($product->id,$product->name,1,$product->price)->associate('App\Models\Product');
         
         if($result){
            
            $response['status']=true;
            $response['cart_count']= Cart::instance('shopping')->count();
            $response['message']= 'Product added sccessfully to cart!!';
            
        }
        
        if($request->ajax()){  
            
            $wishlist = view('frontend.layouts._wishlist')->render();
            $response['wishlist_list']= $wishlist;   
            $header = view('frontend.layouts.main_header')->render();
            $response['header']= $header;
       
            
        }
  
         return $response;
         
         
    }
    
    
    public function wishlistDelete(Request $request) {
        
        $rowId = $request->input('rowId');
        Cart::instance('wishlist')->remove($rowId);
        
      
            
            $response['status']=true;
            $response['cart_count']= Cart::instance('shopping')->count();
            $response['message']= 'Product removed sccessfully from Wishlist!!';
            
      
        
        if($request->ajax()){  
            
            $wishlist = view('frontend.layouts._wishlist')->render();
            $response['wishlist_list']= $wishlist;  
            $header = view('frontend.layouts.main_header')->render();
            $response['header']= $header;      
            
        }
   
         return $response;
        
    }
    
    
}
