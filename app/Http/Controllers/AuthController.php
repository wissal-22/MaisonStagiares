<?php

namespace App\Http\Controllers;

use App\Models\CandidatProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();

            return $this->redirectBasedOnRole($user)
                ->with('success', "Bienvenue, {$user->name} ({$user->role_label}) !");
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent à aucun compte actif.',
        ])->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'genre' => 'required|in:garcon,fille',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'candidat',
        ]);

        CandidatProfile::create([
            'user_id' => $user->id,
            'genre' => $validated['genre'],
        ]);

        Auth::login($user);

        return redirect()->route('candidat.preinscription')
            ->with('success', 'Votre compte candidat a été créé avec succès ! Complétez maintenant votre dossier de préinscription.');
    }

    public function quickLogin(string $role): RedirectResponse
    {
        $user = match ($role) {
            'admin' => User::where('role', 'admin')->first(),
            'gestionnaire' => User::where('role', 'gestionnaire')->first(),
            'directeur' => User::where('role', 'directeur')->first(),
            'direction_regionale' => User::where('role', 'direction_regionale')->first(),
            'direction_centrale' => User::where('role', 'direction_centrale')->first(),
            'candidat' => User::where('role', 'candidat')->first(),
            default => null,
        };

        if ($user) {
            Auth::login($user);
            request()->session()->regenerate();

            return $this->redirectBasedOnRole($user)
                ->with('success', "Connecté en tant que {$user->name} ({$user->role_label})");
        }

        return redirect()->route('login')->with('error', 'Compte de démonstration introuvable.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Vous avez été déconnecté avec succès.');
    }

    private function redirectBasedOnRole(User $user): RedirectResponse
    {
        if ($user->isCandidat()) {
            return redirect()->route('candidat.dashboard');
        }

        return redirect()->route('dashboard');
    }
}
