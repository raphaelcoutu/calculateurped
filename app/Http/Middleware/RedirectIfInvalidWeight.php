<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfInvalidWeight
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $weight = session('app.dosingWeight');

        if(!is_numeric($weight) || $weight <= 0) {
            return redirect()->to('/');
        }

        return $next($request);
    }
}
