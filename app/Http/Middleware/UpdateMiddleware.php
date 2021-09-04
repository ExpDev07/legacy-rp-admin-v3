<?php

namespace App\Http\Middleware;

use App\Helpers\LoggingHelper;
use App\Helpers\SessionHelper;
use Closure;
use Illuminate\Http\Request;

/**
 * Middleware to check if the panel is getting updated
 *
 * @package App\Http\Middleware
 */
class UpdateMiddleware
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
        if (file_exists(__DIR__ . '/../../../update')) {
            abort(503, 'Update in progress');
        }

        return $next($request);
    }

}
