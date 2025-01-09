<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next,...$roles)
    {
        // dd($roles);
        // dd($request->route()->getName());
        // dd($request->route()->getActionname());
        if($request->user()->hasAnyRole($roles)||!$roles){
            return $next($request);
        }
        return redirect()->route("login");
//        return response("Insufficient premissions",401);
    }
}
