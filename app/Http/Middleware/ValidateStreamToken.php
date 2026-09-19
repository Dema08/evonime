<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateStreamToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $episodeId = (int) $request->route('episode');
        $ok = app(\App\Services\StreamTokenService::class)->validate(
            $episodeId,
            (int) $request->query('uid', 0),
            (string) $request->query('token', '')
        );
        abort_unless($ok, 403, 'Invalid or expired stream token.');

        return $next($request);
    }
}
