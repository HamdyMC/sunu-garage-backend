<?php

namespace App\Http\Controllers;

use App\Models\Chauffeur;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChauffeurController extends Controller
{
    /**
     * Affiche une liste de tous les chauffeurs.
     * GET /api/chauffeurs
     */
    public function index()
    {
        // Retourne la liste des chauffeurs paginée pour la performance
        $chauffeurs = Chauffeur::orderBy('nom')->paginate(15);
        
        return response()->json($chauffeurs);
    }

    /**
     * Enregistre un nouveau chauffeur dans la base de données.
     * POST /api/chauffeurs
     */
    public function store(Request $request)
    {
        // Validation des données entrantes
        $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            // Le numéro de téléphone doit être unique dans la table 'chauffeurs'
            'telephone' => ['required', 'string', 'unique:chauffeurs,telephone'], 
            'cni' => ['nullable', 'string', 'max:255'],
            // S'assurer que le statut est une valeur valide de l'enum
            'statut' => ['nullable', Rule::in(['Disponible', 'En attente', 'En voyage', 'Hors service'])],
            'notes' => ['nullable', 'string'],
        ]);

        // Création du chauffeur
        $chauffeur = Chauffeur::create($request->all());

        return response()->json([
            'message' => 'Chauffeur créé avec succès',
            'chauffeur' => $chauffeur
        ], 201); // 201 Created
    }

    /**
     * Affiche les détails d'un chauffeur spécifique.
     * GET /api/chauffeurs/{chauffeur}
     */
    public function show(Chauffeur $chauffeur)
    {
        // Le Chauffeur est automatiquement récupéré par Laravel (Model binding)
        return response()->json($chauffeur);
    }

    /**
     * Met à jour les informations d'un chauffeur.
     * PUT/PATCH /api/chauffeurs/{chauffeur}
     */
    public function update(Request $request, Chauffeur $chauffeur)
    {
        // Validation des données entrantes
        $request->validate([
            'nom' => ['sometimes', 'string', 'max:255'],
            'prenom' => ['sometimes', 'string', 'max:255'],
            // La validation de l'unicité du téléphone doit ignorer le chauffeur actuel
            'telephone' => ['sometimes', 'string', Rule::unique('chauffeurs', 'telephone')->ignore($chauffeur->id)],
            'cni' => ['nullable', 'string', 'max:255'],
            'statut' => ['sometimes', Rule::in(['Disponible', 'En attente', 'En voyage', 'Hors service'])],
            'notes' => ['nullable', 'string'],
        ]);

        // Mise à jour du chauffeur
        $chauffeur->update($request->all());

        return response()->json([
            'message' => 'Chauffeur mis à jour avec succès',
            'chauffeur' => $chauffeur
        ]);
    }

    /**
     * Supprime un chauffeur de la base de données.
     * DELETE /api/chauffeurs/{chauffeur}
     */
    public function destroy(Chauffeur $chauffeur)
    {
        $chauffeur->delete();

        return response()->json([
            'message' => 'Chauffeur supprimé avec succès'
        ], 204); // 204 No Content
    }
}
