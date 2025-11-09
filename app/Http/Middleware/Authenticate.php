<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
{
    if ($request->expectsJson() || $request->is('api/*')) {
        return null; // Return JSON error, don’t redirect
    }

    return route('login');
}

}
