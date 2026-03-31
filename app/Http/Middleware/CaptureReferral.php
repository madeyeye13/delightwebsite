<?php

namespace App\Http\Middleware;

use App\Models\Referral;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Capture ?ref= from the URL and persist it in the session.
 *
 * Register in bootstrap/app.php (Laravel 11) or Kernel.php (Laravel 10):
 *   \App\Http\Middleware\CaptureReferral::class
 * Add to the 'web' middleware group.
 */
class CaptureReferral
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('ref')) {
            $code = strtoupper(trim($request->query('ref')));

            // Validate the code exists before storing it
            if (Referral::where('code', $code)->exists()) {
                // Don't overwrite if already set (first touch wins)
                if (! $request->session()->has('referral_code')) {
                    $request->session()->put('referral_code', $code);
                }
            }
        }

        return $next($request);
    }
}
