<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFaceRegistered
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        // skip request livewire
        if ($request->is('livewire/*')) {
            return $next($request);
        }

        // hanya untuk panel app
        if (!str_starts_with(
            $request->path(),
            'app'
        )) {
            return $next($request);
        }

        $user = auth()->user();

        // kalau belum login
        if (!$user) {
            return $next($request);
        }

        // kalau belum setup wajah
        if (
            $user->intern &&
            empty($user->intern->face_descriptor) &&
            !in_array($request->path(), [
                'app/setup-face',
                'app/logout',
            ])
        ) {
            return redirect('/app/setup-face');
        }

        return $next($request);
    }
}