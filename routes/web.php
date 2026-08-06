<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

// Production: serve the built Vue SPA (frontend/dist copied to public/app by the
// deploy script) for any route that isn't the API or Sanctum's CSRF endpoint, so
// client-side routing (Vue Router) works on a hard refresh / direct link.
// Local dev: the SPA runs on its own Vite dev server (localhost:5173) instead, so this
// route is only ever hit if you request the Laravel dev server's own "/" directly.
Route::get('/{any}', function () {
    $indexPath = public_path('app/index.html');

    if (! File::exists($indexPath)) {
        return response('Frontend build not found. Run the frontend build step (see docs/) and retry.', 404);
    }

    return response()->file($indexPath);
})->where('any', '^(?!api|sanctum).*$');
