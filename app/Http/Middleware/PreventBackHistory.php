<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PreventBackHistory
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (!$response instanceof BinaryFileResponse) {
            return $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
                            ->header('Pragma', 'no-cache')
                            ->header('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');
        }

        return $response;
    }
}
