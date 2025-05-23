<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EntryFormRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\EntryFormCollection;
use App\Http\Resources\EntryFormResource;
use App\Models\EntryForm;
use App\Services\EntryService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EntryFormController extends Controller
{
    protected $entryService;
    
    /**
     * Constructeur avec injection de dépendance du EntryService.
     * 
     * @param EntryService $entryService
     */
    public function __construct(EntryService $entryService)
    {
        $this->entryService = $entryService;
    }

    /**
     * Display a listing of the resource.
     */    public function index()
    {
        $entries = EntryForm::with(['supplier', 'user', 'entryItems', 'entryItems.product'])->paginate(10);
        return (new EntryFormCollection($entries))->additional([
            'success' => true,
            'message' => 'Liste des bons d\'entrée récupérée avec succès'
        ]);
    }    /**
     * Store a newly created resource in storage.
     */    public function store(EntryFormRequest $request)
    {
        // Créer la transaction pour assurer l'intégrité des données
        return DB::transaction(function () use ($request) {
            // La validation est déjà gérée par la classe EntryFormRequest
            $validatedData = $request->validated();
              // Extraire les items du bon d'entrée s'ils existent
            $entryItems = $request->input('items', []);
            
            // Assurez-vous que le total est défini avant la création
            if (!isset($validatedData['total'])) {
                $validatedData['total'] = 0; // Valeur par défaut
            }
            
            // Créer le bon d'entrée
            $entry = EntryForm::create($validatedData);
            
            // Log pour debug
            \Log::info("Bon d'entrée créé avec ID: " . $entry->id . " et " . count($entryItems) . " items");
            
            // Ajouter les items au bon d'entrée
            $total = 0;
            foreach ($entryItems as $item) {
                try {
                    // Créer l'item avec une référence explicite à la clé étrangère
                    $entryItem = new \App\Models\EntryItem([
                        'entry_form_id' => $entry->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'] ?? 0,
                        'total' => isset($item['unit_price']) ? $item['quantity'] * $item['unit_price'] : 0
                    ]);
                    $entryItem->save();
                    
                    // Calculer le total du bon d'entrée
                    $total += $entryItem->total;
                    
                    \Log::info("Item créé: " . json_encode($entryItem->toArray()));
                } catch (\Exception $e) {
                    \Log::error("Erreur lors de la création d'un item: " . $e->getMessage());
                    throw $e;
                }
            }
            
            // Mettre à jour le total du bon d'entrée
            if (count($entryItems) > 0) {
                $entry->total = $total;
                $entry->save();
                \Log::info("Total du bon mis à jour: " . $total);
            }            
            // Charger les relations pour la réponse
            $entry->refresh();
            $entry->load(['supplier', 'user', 'entryItems', 'entryItems.product']);
            
            // Ajouter des informations de diagnostic dans la réponse
            $debugInfo = [
                'item_count' => $entry->items()->count(),
                'items_processed' => count($entryItems),
                'items_details' => $entry->items()->get()->toArray()
            ];
            
            return ApiResponse::success(
                (new EntryFormResource($entry))->additional(['debug' => $debugInfo]),
                'Bon d\'entrée créé avec succès',
                201
            );
        });
    }

    /**
     * Display the specified resource.
     */    public function show(string $id)
    {
        $entry = EntryForm::with(['supplier', 'user', 'entryItems', 'entryItems.product'])->find($id);
        if (!$entry) {
            return ApiResponse::notFound('Bon d\'entrée non trouvé');
        }
        return ApiResponse::success(new EntryFormResource($entry), 'Bon d\'entrée récupéré avec succès');
    }    /**
     * Update the specified resource in storage.
     */    public function update(EntryFormRequest $request, string $id)
    {
        $entry = EntryForm::find($id);
        if (!$entry) {
            return ApiResponse::notFound('Bon d\'entrée non trouvé');
        }
        
        // Vérifier si le bon peut être modifié
        if ($entry->status !== 'draft') {
            return ApiResponse::error('Seuls les bons avec le statut "draft" peuvent être modifiés', 422);
        }
        
        // Transaction pour assurer l'intégrité des données
        return DB::transaction(function () use ($request, $entry) {
            // La validation est déjà gérée par la classe EntryFormRequest
            $validatedData = $request->validated();
            
            // Mettre à jour le bon d'entrée
            $entry->update($validatedData);
            
            // Si des items sont inclus dans la requête, les mettre à jour
            if ($request->has('items')) {
                // Supprimer les items existants
                $entry->items()->delete();
                
                // Ajouter les nouveaux items
                $total = 0;
                foreach ($request->input('items') as $item) {
                    $entryItem = $entry->items()->create([
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total' => $item['quantity'] * $item['unit_price']
                    ]);
                    
                    $total += $entryItem->total;
                }
                
                // Mettre à jour le total du bon d'entrée
                $entry->total = $total;
                $entry->save();
            }
            
            $entry->load(['supplier', 'user', 'entryItems', 'entryItems.product']);
            return ApiResponse::success(new EntryFormResource($entry), 'Bon d\'entrée mis à jour avec succès');
        });
    }

    /**
     * Remove the specified resource from storage.
     */    public function destroy(string $id)
    {
        $entry = EntryForm::find($id);
        if (!$entry) {
            return ApiResponse::notFound('Bon d\'entrée non trouvé');
        }
        $entry->delete();
        return ApiResponse::success(null, 'Bon d\'entrée supprimé avec succès');
    }    /**
     * Valider un bon d'entrée et mettre à jour le stock.
     *
     * @param string $id Identifiant du bon d'entrée.
     * @param Request $request La requête HTTP contenant des informations supplémentaires.
     * @return \Illuminate\Http\JsonResponse
     */
    public function validate(string $id, Request $request)
    {
        try {
            $entry = EntryForm::with(['supplier', 'user', 'entryItems', 'entryItems.product'])->find($id);
            if (!$entry) {
                return ApiResponse::notFound('Bon d\'entrée non trouvé');
            }
              // Vérifier les doublons potentiels avant la validation
            $duplicates = $this->entryService->detectDuplicates([
                'id' => $entry->id,
                'reference' => $entry->reference
            ]);
            
            // Si des doublons sont détectés et que l'utilisateur n'a pas confirmé, renvoyer un avertissement
            if ($duplicates->count() > 0 && !$request->input('confirm_duplicates', false)) {
                return ApiResponse::error(
                    'Des doublons potentiels ont été détectés. Veuillez confirmer la validation.',
                    [
                        'duplicates' => $duplicates->map(function($e) {
                            return ['id' => $e->id, 'reference' => $e->reference];
                        }),
                        'requires_confirmation' => true
                    ],
                    409
                );
            }
            
            // Validation du bon d'entrée avec note optionnelle
            $validatedEntry = $this->entryService->validate($entry, $request->input('validation_note'));
            
            return ApiResponse::success(
                new EntryFormResource($validatedEntry), 
                'Bon d\'entrée validé avec succès'
            );
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 422);
        }
    }

    /**
     * Debug a specific entry form with direct DB query.
     *
     * @param string $id Identifiant du bon d'entrée.
     * @return \Illuminate\Http\JsonResponse
     */
    public function debug(string $id)
    {
        // Récupérer le bon directement depuis la base de données
        $entry = EntryForm::find($id);
        
        if (!$entry) {
            return response()->json(['error' => 'Bon d\'entrée non trouvé'], 404);
        }
        
        // Récupérer les items avec une requête directe
        $items = \DB::table('entry_items')
            ->where('entry_form_id', $id)
            ->get();
        
        // Retourner toutes les données brutes pour débogage
        return response()->json([
            'entry_form' => $entry->toArray(),
            'items_via_relation' => $entry->items()->get()->toArray(),
            'items_via_sql' => $items->toArray(),
            'debug_info' => [
                'entries_count' => EntryForm::count(),
                'all_items_count' => \DB::table('entry_items')->count(),
                'expected_relation' => 'entry_form_id = ' . $id,
                'table_name' => (new \App\Models\EntryItem())->getTable()
            ]
        ]);
    }

    /**
     * Vérifie les doublons potentiels pour un bon d'entrée.
     *
     * @param Request $request La requête HTTP contenant les données à vérifier.
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkDuplicates(Request $request)
    {
        $data = $request->all();
        $duplicates = $this->entryService->detectDuplicates($data);
        
        $formattedDuplicates = $duplicates->map(function($entry) {
            return [
                'id' => $entry->id,
                'reference' => $entry->reference,
                'date' => $entry->date,
                'supplier' => [
                    'id' => $entry->supplier->id,
                    'name' => $entry->supplier->name,
                ],
                'total' => $entry->total,
                'status' => $entry->status,
                'items_count' => $entry->items->count(),
            ];
        });
        
        return ApiResponse::success(
            [
                'has_duplicates' => $duplicates->count() > 0,
                'duplicates' => $formattedDuplicates,
            ],
            'Vérification des doublons effectuée'
        );
    }

    /**
     * Annule un bon d'entrée.
     *
     * @param string $id Identifiant du bon d'entrée.
     * @param Request $request La requête HTTP contenant la raison de l'annulation.
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel(string $id, Request $request)
    {
        try {
            $entry = EntryForm::with(['supplier', 'user', 'entryItems', 'entryItems.product'])->find($id);
            if (!$entry) {
                return ApiResponse::notFound('Bon d\'entrée non trouvé');
            }
            
            // Valider la requête pour la raison de l'annulation
            $request->validate([
                'cancel_reason' => 'nullable|string|max:500'
            ]);
            
            $cancelledEntry = $this->entryService->cancel($entry, $request->input('cancel_reason'));
            return ApiResponse::success(
                new EntryFormResource($cancelledEntry), 
                'Bon d\'entrée annulé avec succès'
            );
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage(), [], 422);
        }
    }

    /**
     * Récupère l'historique des modifications d'un bon d'entrée.
     *
     * @param string $id Identifiant du bon d'entrée.
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(string $id)
    {
        $entry = EntryForm::with(['histories.user'])->find($id);
        if (!$entry) {
            return ApiResponse::notFound('Bon d\'entrée non trouvé');
        }
        
        $history = $entry->histories->map(function($item) {
            return [
                'id' => $item->id,
                'field' => $item->field_name,
                'old_value' => $item->old_value,
                'new_value' => $item->new_value,
                'reason' => $item->change_reason,
                'user' => $item->user ? [
                    'id' => $item->user->id,
                    'name' => $item->user->name,
                ] : null,
                'date' => $item->created_at,
            ];
        });
        
        return ApiResponse::success(
            [
                'entry_id' => $entry->id,
                'reference' => $entry->reference,
                'history' => $history
            ],
            'Historique récupéré avec succès'
        );
    }

    /**
     * Génère un rapport des entrées par période.
     *
     * @param Request $request La requête HTTP contenant les dates de début et de fin.
     * @return \Illuminate\Http\JsonResponse
     */
    public function reportByPeriod(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
        
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $report = $this->entryService->getEntriesByPeriod($startDate, $endDate);
        
        return ApiResponse::success(
            $report,
            'Rapport des entrées par période généré avec succès'
        );
    }

    /**
     * Génère un rapport des entrées par fournisseur.
     *
     * @param Request $request La requête HTTP contenant l'ID du fournisseur et les dates optionnelles.
     * @return \Illuminate\Http\JsonResponse
     */
    public function reportBySupplier(Request $request)
    {
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        
        $supplierId = $request->input('supplier_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $report = $this->entryService->getEntriesBySupplier($supplierId, $startDate, $endDate);
        
        return ApiResponse::success(
            $report,
            'Rapport des entrées par fournisseur généré avec succès'
        );
    }

    /**
     * Génère un rapport des entrées par produit.
     *
     * @param Request $request La requête HTTP contenant l'ID du produit et les dates optionnelles.
     * @return \Illuminate\Http\JsonResponse
     */
    public function reportByProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        
        $productId = $request->input('product_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $report = $this->entryService->getEntriesByProduct($productId, $startDate, $endDate);
        
        return ApiResponse::success(
            $report,
            'Rapport des entrées par produit généré avec succès'
        );
    }
}
