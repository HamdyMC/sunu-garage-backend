<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChauffeurController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\TourDeRoleController;

/*
|--------------------------------------------------------------------------
| Routes API Publiques (Authentification)
|--------------------------------------------------------------------------
| Ces routes ne nécessitent PAS d'être authentifié via un token.
| Elles sont utilisées pour se connecter et obtenir un token Sanctum.
*/

// Routes pour l'authentification (Connexion et Déconnexion)
Route::controller(AuthController::class)->group(function () {
    // POST /api/login - Connexion de l'utilisateur et génération du token
    Route::post('/login', 'login');
});


/*
|--------------------------------------------------------------------------
| Routes API Protégées (Nécessitent un Token Sanctum)
|--------------------------------------------------------------------------
| Toutes les routes dans ce groupe exigeront le header 'Authorization: Bearer <token>'
| pour être accédées.
*/

Route::middleware('auth:sanctum')->group(function () {

    // POST /api/logout - Déconnexion de l'utilisateur (révocation du token)
    Route::post('/logout', [AuthController::class, 'logout']);


    // ----------------------------------------------------------------------
    // 1. Gestion des Chauffeurs (CRUD)
    // accessible par l'Admin et le Chef de Garage
    // ----------------------------------------------------------------------
    // GET /api/chauffeurs - Liste de tous les chauffeurs
    // POST /api/chauffeurs - Créer un nouveau chauffeur
    // GET /api/chauffeurs/{chauffeur} - Afficher un chauffeur spécifique
    // PUT/PATCH /api/chauffeurs/{chauffeur} - Mettre à jour un chauffeur
    // DELETE /api/chauffeurs/{chauffeur} - Supprimer un chauffeur
    Route::apiResource('chauffeurs', ChauffeurController::class);


    // ----------------------------------------------------------------------
    // 2. Gestion des Véhicules (CRUD)
    // accessible par l'Admin et le Chef de Garage
    // ----------------------------------------------------------------------
    // GET /api/vehicules - Liste de tous les véhicules
    // POST /api/vehicules - Créer un nouveau véhicule
    // ... et autres méthodes CRUD
    Route::apiResource('vehicules', VehiculeController::class);


    // ----------------------------------------------------------------------
    // 3. Gestion du Tour de Rôle / File d'attente
    // accessible principalement par le Chef de Garage
    // ----------------------------------------------------------------------
    Route::apiResource('tours-de-role', TourDeRoleController::class)->names('tours-de-role');

    // Route spécifique pour ajouter un chauffeur à la file d'attente (en dehors du CRUD standard)
    // POST /api/tours-de-role/add-to-queue
    Route::post('tours-de-role/add-to-queue', [TourDeRoleController::class, 'addToQueue']);

    // Route spécifique pour attribuer un départ à un chauffeur (quitter la file)
    // POST /api/tours-de-role/dispatch/{tourDeRole}
    Route::post('tours-de-role/dispatch/{tourDeRole}', [TourDeRoleController::class, 'dispatch']);

});
