<?php

namespace ApiArchitect\Auth\Http\Controllers\Middleware;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class RoleMiddleware
{

    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(ServerRequestInterface $request, Closure $next, $role)
    {
        dd($this->auth->user);
        $user = $this->auth->parseToken()->authenticate();
        if (!$user->hasRole($role)) {
            return redirect('/login');
        }

        return $next($request);
    }

}