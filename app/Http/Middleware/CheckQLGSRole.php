<?php

namespace App\Http\Middleware;

use Closure;

class CheckQLGSRole
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
        // Kiểm tra xem người dùng đã đăng nhập hay chưa
        if (auth()->check() && auth()->user()->roles !== 'QLGS') {
            abort(403, 'Bạn không có quyền truy cập vào trang này.');
        }
    
        return $next($request);
    }
}
