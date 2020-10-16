<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = [
            'message' => 'unauthorized user'
        ];
        
        $user = User::select('users.*')
        ->where('token', $request->token)
        ->join('tokens', 'tokens.user_id', 'users.id')
        ->first();
        
        if(!$request->token && !$user){
            return response()->json($response, 403);
        }
        
        $request->attributes->set('user', $user);
        return $next($request);
    }
}
