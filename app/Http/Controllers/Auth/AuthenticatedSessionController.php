<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Vérifier si le compte est actif
        if (!$user->is_active || $user->is_blocked) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Votre compte est désactivé ou bloqué. Contactez l\'administrateur.',
            ]);
        }

        // Enregistrer la connexion
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        return redirect()->intended($this->redirectPath($user));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Get the post-login redirect path.
     */
    protected function redirectPath($user): string
    {
        if ($user->hasRole(['super_admin', 'admin', 'manager'])) {
            return route('admin.dashboard');
        } elseif ($user->hasRole(['chef', 'waiter'])) {
            return route('admin.orders.index');
        } elseif ($user->hasRole('delivery_person')) {
            return route('delivery.dashboard');
        }

        return route('home');
    }

}
