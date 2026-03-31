<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->isAdmin()) {
            return $next($request);
        }

        if ($user->isStaff() && $user->is_active) {
            $segment = $request->segment(2) ?? 'dashboard';
            if ($user->canAccessAdminPage($segment)) {
                return $next($request);
            }

            abort(403, 'You do not have permission to access this area.');
        }

        abort(403, 'Unauthorized');
    }
}
