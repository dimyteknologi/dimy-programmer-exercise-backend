<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ErrorResponseResource;

class ModelAuthorization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permission_name)
    {
        try {
            if (Auth::check() && Auth::user()->hasPermissionTo($permission_name, 'api')) {
                return $next($request);
            }

            return new ErrorResponseResource(__('general.permission.access.reject'), 403);
        } catch (\Exception $e) {
            return new ErrorResponseResource(__('general.permission.access.reject'), 403);
        }
    }
}
