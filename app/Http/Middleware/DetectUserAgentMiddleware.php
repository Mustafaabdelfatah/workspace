<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;

class DetectUserAgentMiddleware
{
    /**
     * Handle an incoming language from request headers.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = strtolower($request->header('User-Agent', ''));

        $agent = 'web';  // Default

        if (strpos($userAgent, 'android') !== false) {
            $agent = 'android';
        } elseif (strpos($userAgent, 'iphone') !== false || strpos($userAgent, 'ipad') !== false ||  strpos($userAgent, 'ios') !== false) {
            $agent = 'ios';
        } elseif (strpos($userAgent, 'windows') !== false || strpos($userAgent, 'macintosh') !== false) {
            $agent = 'web';
        }

        // Inject 'agent' into request for downstream use
        $request->merge(['agent' => $agent]);

        return $next($request);
    }
}
