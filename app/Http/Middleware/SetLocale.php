<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Locales supportées par l'application.
     */
    protected array $supportedLocales = ['fr', 'en'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Priorité : session → cookie → config par défaut
        $locale = session('locale')
            ?? $request->cookie('locale')
            ?? config('app.locale', 'fr');

        // Sécurité : n'utiliser que les locales autorisées
        if (! in_array($locale, $this->supportedLocales)) {
            $locale = config('app.locale', 'fr');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
