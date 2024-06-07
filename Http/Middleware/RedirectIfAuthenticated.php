<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{

    
    public function handle($request, Closure $next)
    {
        

        
       if(Auth::check() && Auth::user()->role == 'customer' ){
            
            
            return redirect()->route('userDashboard');
        }
        
        elseif(Auth::check() && Auth::user()->role == 'seller'){
            
            
            return redirect()->route('sellerDashboard');
            
        } elseif(Auth::check() && Auth::user()->role == 'admin'){
            
            
            return redirect()->route('admin');
            
            
        } else{
            
            return $next($request);
        }
        
        
        
                
//        if (Auth::check()) {
//            
//            return redirect('/');
//        }
//
//        return $next($request);
        
      
        
        
        
        
        
   }
   
   
   
   
    
}
