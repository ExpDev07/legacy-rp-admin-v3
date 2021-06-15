<?php

namespace App\Http\Middleware;

use App\Helpers\LoggingHelper;
use App\Helpers\SessionHelper;
use Closure;
use Illuminate\Http\Request;

/**
 * Middleware to log requests
 *
 * @package App\Http\Middleware
 */
class LogMiddleware
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
        $session = SessionHelper::getInstance();
        LoggingHelper::log($session->getSessionKey(), 'ACCEPTED');

        return $next($request);
    }

}
