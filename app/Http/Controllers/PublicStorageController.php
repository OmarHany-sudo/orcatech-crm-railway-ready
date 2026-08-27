<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Fallback for hosts where public/storage cannot be created as a symlink.
 * Only the explicitly public disk is exposed; private local storage remains
 * outside the web root and is never resolved by this controller.
 */
final class PublicStorageController extends Controller
{
    public function __invoke(string $path): Response|StreamedResponse
    {
        $path = ltrim(str_replace('\\', '/', $path), '/');

        if ($path === '' || str_contains($path, '..')) {
            abort(404);
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            abort(404);
        }

        return $disk->response($path);
    }
}
