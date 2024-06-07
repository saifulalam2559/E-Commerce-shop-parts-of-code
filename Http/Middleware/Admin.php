<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;




class Admin
{

    public function handle(Request $request, Closure $next)
    {
        
        
        if(auth()->user()->role == 'admin'){
            
            return $next($request);
            
        }elseif(auth()->user()->role == 'seller'){
            
            return redirect()->route('sellerDashboard');
            
        }
        
        
               elseif(auth()->user()->role == 'customer'){
            
            return redirect()->route('userDashboard');
            
        }
        
        
        
        else{
            
            return redirect()->route(auth()->user()->role)->with('error','You do not have access here !!');
        }
        
        
    }
    
    
    
}
