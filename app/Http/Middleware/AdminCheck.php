<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        //$x = $request->user();
        if($request->user('api')){
            $user = $request->user('api');
            if($user->id_role==2){
                return $next($request);
            }
            else{
                return response()->json([
                    'message' => 'Không có quyền quản trị'
                ],403);
            }
        }
        else{
            return response()->json([
                'message' => 'Chưa đăng nhập'
            ],401);
        }
    }
}
