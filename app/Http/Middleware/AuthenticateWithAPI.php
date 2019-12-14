<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateWithAPI
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = config('app.api_key');
        if ($request->apiKey !== $apiKey) {
            abort(403);
        }

        return $next($request);
    }
}
