<?php

namespace App\Http\Middleware;

use App\Models\AppSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        // Allow admin area, auth routes, and health-check through always
        if (
            $request->is('admin*') ||
            $request->is('login*') ||
            $request->is('logout*') ||
            $request->is('register*') ||
            $request->is('password*') ||
            $request->is('up')
        ) {
            return $next($request);
        }

        // Allow authenticated admin / staff users through unimpeded
        $user = auth()->user();
        if ($user && ($user->isAdmin() || $user->isStaff())) {
            return $next($request);
        }

        if ((bool) AppSetting::get('maintenance_mode', '0')) {
            return response()->view('maintenance', [], 503);
        }

        return $next($request);
    }
}
