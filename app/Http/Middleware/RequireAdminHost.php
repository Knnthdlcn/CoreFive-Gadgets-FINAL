<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdminHost
{
    /**
     * If ADMIN_HOST is set, only allow admin routes on that host.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $adminHost = (string) config('app.admin_host', '');

        if ($adminHost !== '') {
            $requestHost = $request->getHost();

            if (!hash_equals($adminHost, $requestHost)) {
                abort(404);
            }
        }

        return $next($request);
    }
}
