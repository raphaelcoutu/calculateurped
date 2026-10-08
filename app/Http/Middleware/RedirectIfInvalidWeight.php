<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfInvalidWeight
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $weight = session('app.dosingWeight');

        if (! is_numeric($weight) || $weight <= 0) {
            return redirect()->to('/');
        }

        return $next($request);
    }
}
