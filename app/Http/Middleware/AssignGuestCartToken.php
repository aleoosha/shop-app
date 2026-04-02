<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use \Illuminate\Support\Str;

class AssignGuestCartToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
    if (!$request->user() && !$request->hasCookie('cart_token')) {
        $guestId = (string) Str::uuid();
        
        $request->cookies->set('cart_token', $guestId);
        
        $response = $next($request);
        return $response->withCookie(cookie()->make('cart_token', $guestId, 60 * 24 * 30));
    }

        return $next($request);
    }
}
