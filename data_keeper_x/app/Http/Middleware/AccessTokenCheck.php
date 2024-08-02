<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AccessTokenCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            is_null($request->bearerToken()) ||
            \App\Classes\AccessToken\AccessToken::isExpired($request->bearerToken())
        ) {
            return response(null, 401);
        }
        return $next($request);
    }
}
