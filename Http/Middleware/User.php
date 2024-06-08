<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class User
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role == "seller") {
            return redirect()->route("sellerDashboard");
        } elseif (auth()->user()->role == "admin") {
            return redirect()->route("admin");
        } elseif (auth()->user()->role == "customer") {
            return $next($request);
        } else {
            return redirect()
                ->route(auth()->user()->role)
                ->with("error", "You do not have access here !!");
        }
    }
}
