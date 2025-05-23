<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExitFormRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\ExitFormCollection;
use App\Http\Resources\ExitFormResource;
use App\Models\ExitForm;
use App\Services\ExitService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExitFormController extends Controller
{
    protected $exitService;
    
    /**
     * Constructeur avec injection de dépendance du ExitService.
     * 
     * @param ExitService $exitService
     */
    public function __construct(ExitService $exitService)
    {
        $this->exitService = $exitService;
    }

    /**
     * Display a listing of the resource.
     */    public function index()
    {
        $exits = ExitForm::with(['user', 'exitItems', 'exitItems.product'])->paginate(10);
        return (new ExitFormCollection($exits))->additional([
            'success' => true,
            'message' => 'Liste des bons de sortie récupérée avec succès'
        ]);
    }    /**
     * Store a newly created resource in storage.
     */    public function store(ExitFormRequest $request)
    {
        // Créer la transaction pour assurer l'intégrité des données
        return DB::transaction(function () use ($request) {
            // La validation est déjà gérée par la classe ExitFormRequest
            $validatedData = $request->validated();
              // Extraire les items du bon de sortie s'ils existent
            $exitItems = $request->input('items', []);
            
            // Assurez-vous que les champs nécessaires sont définis
            if (!isset($validatedData['status'])) {
                $validatedData['status'] = 'draft'; // Valeur par défaut
            }
            
            // Créer le bon de sortie
            $exit = ExitForm::create($validatedData);
            
            // Log pour debug
            \Log::info("Bon de sortie créé avec ID: " . $exit->id . " et " . count($exitItems) . " items");
              // Ajouter les items au bon de sortie
            foreach ($exitItems as $item) {
                try {
                    // Créer l'item avec une référence explicite à la clé étrangère
                    $exitItem = new \App\Models\ExitItem([
                        'exit_form_id' => $exit->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity']
                    ]);
                    $exitItem->save();
                    
                    \Log::info("Item de sortie créé: " . json_encode($exitItem->toArray()));
                } catch (\Exception $e) {
                    \Log::error("Erreur lors de la création d'un item de sortie: " . $e->getMessage());
                    throw $e;
                }
            }
            
            // Charger les relations pour la réponse
            $exit->load(['user', 'exitItems', 'exitItems.product']);
            
            return ApiResponse::success(new ExitFormResource($exit), 'Bon de sortie créé avec succès', 201);
        });
    }

    /**
     * Display the specified resource.
     */    public function show(string $id)
    {
        $exit = ExitForm::with(['user', 'exitItems', 'exitItems.product'])->find($id);
        if (!$exit) {
            return ApiResponse::notFound('Bon de sortie non trouvé');
        }
        return ApiResponse::success(new ExitFormResource($exit), 'Bon de sortie récupéré avec succès');
    }    /**
     * Update the specified resource in storage.
     */    public function update(ExitFormRequest $request, string $id)
    {
        $exit = ExitForm::find($id);
        if (!$exit) {
            return ApiResponse::notFound('Bon de sortie non trouvé');
        }
        
        // Vérifier si le bon peut être modifié
        if ($exit->status !== 'draft') {
            return ApiResponse::error('Seuls les bons avec le statut "draft" peuvent être modifiés', 422);
        }
        
        // Transaction pour assurer l'intégrité des données
        return DB::transaction(function () use ($request, $exit) {
            // La validation est déjà gérée par la classe ExitFormRequest
            $validatedData = $request->validated();
            
            // Mettre à jour le bon de sortie
            $exit->update($validatedData);
            
            // Si des items sont inclus dans la requête, les mettre à jour
            if ($request->has('items')) {
                // Supprimer les items existants
                $exit->items()->delete();
                
                // Ajouter les nouveaux items
                foreach ($request->input('items') as $item) {
                    $exit->items()->create([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity']
                    ]);
                }
            }
            
            $exit->load(['user', 'exitItems', 'exitItems.product']);
            return ApiResponse::success(new ExitFormResource($exit), 'Bon de sortie mis à jour avec succès');
        });
    }

    /**
     * Remove the specified resource from storage.
     */    public function destroy(string $id)
    {
        $exit = ExitForm::find($id);
        if (!$exit) {
            return ApiResponse::notFound('Bon de sortie non trouvé');
        }
        $exit->delete();
        return ApiResponse::success(null, 'Bon de sortie supprimé avec succès');
    }    /**
     * Valider un bon de sortie et mettre à jour le stock.
     *
     * @param string $id Identifiant du bon de sortie.
     * @param Request $request Requête contenant une note optionnelle
     * @return \Illuminate\Http\JsonResponse
     */
    public function validate(string $id, Request $request = null)
    {
        try {
            $exit = ExitForm::with(['user', 'exitItems', 'exitItems.product'])->find($id);
            if (!$exit) {
                return ApiResponse::notFound('Bon de sortie non trouvé');
            }
            
            $validationNote = $request ? $request->input('validation_note') : null;
            $validatedExit = $this->exitService->validate($exit, $validationNote);
            
            return ApiResponse::success(
                new ExitFormResource($validatedExit), 
                'Bon de sortie validé avec succès'
            );
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 422);
        }
    }

    /**
     * Annuler un bon de sortie et ajuster le stock si nécessaire.
     *
     * @param string $id Identifiant du bon de sortie.
     * @param Request $request Requête contenant une raison d'annulation optionnelle
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(string $id, Request $request)
    {
        try {
            $exit = ExitForm::with(['user', 'exitItems', 'exitItems.product'])->find($id);
            if (!$exit) {
                return ApiResponse::notFound('Bon de sortie non trouvé');
            }
            
            $reason = $request->input('reason');
            $cancelledExit = $this->exitService->cancel($exit, $reason);
            
            return ApiResponse::success(
                new ExitFormResource($cancelledExit), 
                'Bon de sortie annulé avec succès'
            );
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 422);
        }
    }

    /**
     * Récupérer l'historique des modifications d'un bon de sortie.
     *
     * @param string $id Identifiant du bon de sortie.
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(string $id)
    {
        $exit = ExitForm::find($id);
        if (!$exit) {
            return ApiResponse::notFound('Bon de sortie non trouvé');
        }
        
        $history = $exit->histories()->with('user')->orderBy('created_at', 'desc')->get();
        
        return ApiResponse::success([
            'exit_form' => new ExitFormResource($exit),
            'history' => $history
        ], 'Historique des modifications récupéré avec succès');
    }

    /**
     * Vérifier les doublons potentiels pour un bon de sortie.
     *
     * @param Request $request Requête contenant les données à vérifier.
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkDuplicates(Request $request)
    {
        $data = $request->all();
        $duplicates = $this->exitService->detectDuplicates($data);
        
        return ApiResponse::success([
            'has_duplicates' => $duplicates->isNotEmpty(),
            'duplicates' => $duplicates->isEmpty() ? [] : ExitFormResource::collection($duplicates)
        ], 'Vérification des doublons effectuée avec succès');
    }

    /**
     * Générer un rapport des sorties par période.
     *
     * @param Request $request Requête contenant les dates de début et de fin.
     * @return \Illuminate\Http\JsonResponse
     */
    public function reportByPeriod(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date|date_format:Y-m-d',
            'end_date' => 'required|date|date_format:Y-m-d|after_or_equal:start_date'
        ]);
        
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $report = $this->exitService->getExitsByPeriod($startDate, $endDate);
        
        return ApiResponse::success($report, 'Rapport des sorties généré avec succès');
    }

    /**
     * Générer un rapport des sorties par destination.
     *
     * @param Request $request Requête contenant les dates de début et de fin optionnelles.
     * @return \Illuminate\Http\JsonResponse
     */
    public function reportByDestination(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'end_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:start_date'
        ]);
        
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $report = $this->exitService->getExitsByDestination($startDate, $endDate);
        
        return ApiResponse::success($report, 'Rapport des sorties par destination généré avec succès');
    }
}
