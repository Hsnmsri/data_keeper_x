<?php

namespace App\Http\Middleware;

use App\Classes\AccessToken\AccessToken;
use App\Classes\ApiSecretToken\ApiSecretToken;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientRequestCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // check required fields
        if (
            is_null($request->bearerToken()) ||
            \App\Classes\AccessToken\AccessToken::isExpired($request->bearerToken()) ||
            !$request->has('api_secret')
        ) {
            return response(null, 401);
        }

        // check user id in access token and api secret
        if (AccessToken::getUserIdFromToken($request->bearerToken()) !== ApiSecretToken::getUserIdFromToken($request->api_secret)) {
            return response(null, 401);
        }

        // check api secret
        if (User::find(ApiSecretToken::getUserIdFromToken($request->api_secret))->api_secret !== $request->api_secret) {
            return response(null, 401);
        }

        return $next($request);
    }
}
