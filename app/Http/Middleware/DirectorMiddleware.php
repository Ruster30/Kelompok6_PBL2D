<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DirectorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route("login");
        }

        if ($request->user()->role !== "director") {
            abort(403, "Akses ditolak. Halaman ini khusus Director.");
        }

        return $next($request);
    }
}