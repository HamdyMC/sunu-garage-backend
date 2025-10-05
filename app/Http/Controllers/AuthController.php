<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Gère la tentative de connexion de l'utilisateur et l'émission du token Sanctum.
     */
    public function login(Request $request)
    {
        // 1. Validation des données d'entrée
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            // Le 'device_name' est facultatif mais bon pour la traçabilité
            'device_name' => ['string', 'nullable'],
        ]);

        // 2. Tentative d'authentification
        // On utilise la fonction 'attempt' qui vérifie les identifiants
        if (! Auth::attempt($request->only('email', 'password'))) {
            // Si l'authentification échoue
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis ne correspondent pas à nos enregistrements.'],
            ]);
        }

        // 3. Récupération de l'utilisateur authentifié
        $user = Auth::user();

        // 4. Génération du token Sanctum
        // Le nom de l'appareil est utilisé pour identifier le token
        $token = $user->createToken($request->device_name ?? 'mobile_app')->plainTextToken;

        // 5. Réponse de succès
        return response()->json([
            'message' => 'Connexion réussie',
            'user' => $user,
            'token' => $token,
            // Expiration (optionnel, à titre indicatif)
            'token_type' => 'Bearer',
        ], 200);
    }

    /**
     * Déconnecte l'utilisateur en révoquant le token actuel.
     */
    public function logout(Request $request)
    {
        // Supprime le token actuel de l'utilisateur (le token utilisé pour cette requête)
        // L'utilisateur doit être authentifié pour arriver ici (middleware 'auth:sanctum')
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie. Le token a été révoqué.',
        ], 200);
    }
}
