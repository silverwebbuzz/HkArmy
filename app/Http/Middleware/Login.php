<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;
use Session;

class Login
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @param  string|null  $guard
	 * @return mixed
	 */
	public function handle($request, Closure $next, $guard = null)
	{
		if(!empty(Session::get('user'))){
			$login = Session::get('user')['user_id'];	
			if(!$login){
				return redirect('/login');
			}
			return $next($request);	
		}else{
			return redirect('/login');
		}
		return $next($request);		
	}
}
