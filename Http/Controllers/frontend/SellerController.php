<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;



class SellerController extends Controller
{
    
    
    
    
    
    public function sellerDashboard() {
        
         $user = Auth::user();
        // dd($user);
        
         return view('frontend.seller.dashboard', compact('user'));
        
    }
    
    
    public function sellerOrder() {
        
         $user = Auth::user();
        // dd($user);
        
         return view('frontend.seller.order', compact('user'));
        
    }
    
    
        public function sellerAddress() {
        
         $user = Auth::user();
        // dd($user);
        
         return view('frontend.seller.address', compact('user'));
        
    }
    
    
            public function selleraccountDetail() {
        
         $user = Auth::user();
        // dd($user);
        
         return view('frontend.seller.account_detail', compact('user'));
        
    }
    
    
    
    
    
}
