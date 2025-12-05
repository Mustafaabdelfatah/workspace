<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    /**
     * Handle an incoming language from request headers.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = ($request->header('Accept-Language')) ? $request->header('Accept-Language') : 'ar';
        
        if(strlen($locale) > 2){
            $locale = substr($locale, 0, 2);
        }
        app()->setLocale($locale);

        return $next($request);
    }
}
