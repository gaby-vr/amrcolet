<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForgetParameters
{
    public function handle(Request $request, Closure $next, ...$parameters)
    {
        foreach($parameters as $parameter) {
            if(isset($request->route()->parameters()[$parameter])) {
                $request->$parameter = $request->route()->parameters()[$parameter];
            }
            $request->route()->forgetParameter($parameter);
        }

        return $next($request);
    }
}
