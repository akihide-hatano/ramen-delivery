<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // 追加

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ユーザーが認証済みであり、かつ is_admin カラムが true の場合のみ続行
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request);
        }

        // それ以外の場合はホームページにリダイレクト
        return redirect('/');
    }
}