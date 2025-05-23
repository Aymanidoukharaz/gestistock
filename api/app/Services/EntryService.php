<?php

namespace App\Services;

use App\Models\EntryForm;
use App\Models\EntryFormHistory;
use App\Models\EntryItem;
use App\Models\Product;
use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntryService
{
    protected $stockService;

    /**
     * Constructeur du service.
     *
     * @param StockService $stockService
     */
    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }
    
    /**
     * Crée un enregistrement dans l'historique des modifications du bon d'entrée.
     *
     * @param EntryForm $entryForm Le bon d'entrée concerné.
     * @param string $fieldName Le nom du champ modifié.
     * @param mixed $oldValue L'ancienne valeur.
     * @param mixed $newValue La nouvelle valeur.
     * @param string|null $reason La raison de la modification.
     * @return EntryFormHistory L'historique créé.
     */
    public function logHistory(
        EntryForm $entryForm,
        string $fieldName,
        $oldValue,
        $newValue,
        ?string $reason = null
    ): EntryFormHistory {
        return EntryFormHistory::create([
            'entry_form_id' => $entryForm->id,
            'user_id' => Auth::id() ?? $entryForm->user_id,
            'field_name' => $fieldName,
            'old_value' => is_array($oldValue) ? json_encode($oldValue) : $oldValue,
            'new_value' => is_array($newValue) ? json_encode($newValue) : $newValue,
            'change_reason' => $reason,
        ]);
    }    /**
     * Valider un bon d'entrée et mettre à jour le stock.
     *
     * @param EntryForm $entryForm Bon d'entrée à valider.
     * @param string|null $validationNote Note optionnelle sur la validation
     * @return EntryForm Le bon d'entrée validé.
     * @throws Exception Si le bon d'entrée n'est pas en statut draft ou en cas d'erreur.
     */
    public function validate(EntryForm $entryForm, ?string $validationNote = null): EntryForm
    {
        // Vérifier si le bon est en statut draft
        if ($entryForm->status !== 'draft') {
            throw new Exception("Le bon d'entrée doit être en statut 'draft' pour être validé.");
        }
        
        // Vérifier que le bon contient au moins un item
        if ($entryForm->items()->count() === 0) {
            throw new Exception("Impossible de valider un bon d'entrée sans articles.");
        }
        
        // Vérifier que la date n'est pas dans le futur
        if (!$this->isValidDate($entryForm->date)) {
            throw new Exception("La date du bon d'entrée ne peut pas être dans le futur.");
        }

        // Transaction pour assurer l'intégrité des données
        return DB::transaction(function () use ($entryForm, $validationNote) {
            try {
                // Étape 1: Enregistrer l'état initial dans l'historique
                $this->logHistory(
                    $entryForm,
                    'status',
                    $entryForm->status,
                    'pending',
                    $validationNote ?? "Début du processus de validation"
                );
                
                // Étape 2: Changer le statut en 'pending'
                $entryForm->status = 'pending';
                $entryForm->save();
                
                // Étape 3: Traiter chaque ligne du bon d'entrée
                foreach ($entryForm->entryItems as $item) {
                    $product = Product::findOrFail($item->product_id);
                    
                    // Mettre à jour le stock (quantité et prix si nécessaire)
                    $this->stockService->updateStock($product, $item->quantity, $item->unit_price);
                    
                    // Créer un mouvement de stock pour cette entrée
                    $this->stockService->createStockMovement(
                        $product,
                        'entry',
                        $item->quantity,
                        "Entrée de stock via bon #{$entryForm->reference}"
                    );
                }

                // Étape 4: Changer le statut en 'completed' et enregistrer dans l'historique
                $this->logHistory(
                    $entryForm,
                    'status',
                    'pending',
                    'completed',
                    $validationNote ?? "Fin du processus de validation"
                );
                
                $entryForm->status = 'completed';
                $entryForm->save();

                Log::info("Bon d'entrée #{$entryForm->reference} validé avec succès");
                return $entryForm;
            } catch (Exception $e) {
                Log::error("Erreur lors de la validation du bon d'entrée #{$entryForm->reference}: " . $e->getMessage());
                throw $e;
            }
        });
    }    /**
     * Détecte les doublons potentiels pour un bon d'entrée.
     * Un doublon est défini uniquement par la référence identique.
     *
     * @param array $data Les données du bon d'entrée à vérifier.
     * @return Collection Les bons d'entrée potentiellement en doublon.
     */
    public function detectDuplicates(array $data): Collection
    {
        $query = EntryForm::query();

        // Vérifier uniquement la correspondance sur la référence
        if (isset($data['reference'])) {
            $query->where('reference', $data['reference']);
        } else {
            // Si pas de référence spécifiée, aucun doublon possible
            return collect();
        }

        // Exclure le bon d'entrée en cours d'édition
        if (isset($data['id'])) {
            $query->where('id', '!=', $data['id']);
        }

        // Exclure les bons annulés
        $query->where('status', '!=', 'cancelled');

        return $query->with(['supplier', 'items.product'])->get();
    }

    /**
     * Annule un bon d'entrée et ajuste le stock si nécessaire.
     *
     * @param EntryForm $entryForm Le bon d'entrée à annuler.
     * @param string|null $reason La raison de l'annulation.
     * @return EntryForm Le bon d'entrée annulé.
     * @throws Exception Si le bon d'entrée ne peut pas être annulé.
     */
    public function cancel(EntryForm $entryForm, ?string $reason = null): EntryForm
    {
        // Vérifier si le bon peut être annulé
        if ($entryForm->status === 'cancelled') {
            throw new Exception("Ce bon d'entrée est déjà annulé.");
        }

        $oldStatus = $entryForm->status;

        // Transaction pour assurer l'intégrité des données
        return DB::transaction(function () use ($entryForm, $oldStatus, $reason) {
            try {
                // Si le bon était en statut 'completed', il faut annuler l'impact sur le stock
                if ($oldStatus === 'completed') {
                    foreach ($entryForm->entryItems as $item) {
                        $product = Product::findOrFail($item->product_id);
                        
                        // Annuler l'impact sur le stock (quantité négative pour diminuer)
                        $this->stockService->updateStock($product, -$item->quantity);
                        
                        // Créer un mouvement de stock pour cette annulation
                        $this->stockService->createStockMovement(
                            $product,
                            'exit',
                            $item->quantity,
                            "Annulation du bon d'entrée #{$entryForm->reference}"
                        );
                    }
                }

                // Changer le statut en 'cancelled'
                $entryForm->status = 'cancelled';
                $entryForm->save();

                // Ajouter à l'historique
                $this->logHistory(
                    $entryForm,
                    'status',
                    $oldStatus,
                    'cancelled',
                    $reason
                );

                Log::info("Bon d'entrée #{$entryForm->reference} annulé avec succès. Raison: " . ($reason ?? 'Non spécifiée'));
                return $entryForm;
            } catch (Exception $e) {
                Log::error("Erreur lors de l'annulation du bon d'entrée #{$entryForm->reference}: " . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Génère un rapport des entrées par période.
     *
     * @param string $startDate Date de début au format Y-m-d.
     * @param string $endDate Date de fin au format Y-m-d.
     * @return array Rapport des entrées par période.
     */
    public function getEntriesByPeriod(string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();
        
        $entries = EntryForm::whereBetween('date', [$start, $end])
            ->where('status', 'completed')
            ->with(['supplier', 'items.product'])
            ->get();
            
        $totalAmount = $entries->sum('total');
        $entriesCount = $entries->count();
        $itemsCount = $entries->flatMap->items->count();
        
        return [
            'period' => [
                'start' => $startDate,
                'end' => $endDate
            ],
            'summary' => [
                'total_amount' => $totalAmount,
                'entries_count' => $entriesCount,
                'items_count' => $itemsCount,
                'average_per_entry' => $entriesCount > 0 ? $totalAmount / $entriesCount : 0
            ],
            'entries' => $entries
        ];
    }

    /**
     * Génère un rapport des entrées par fournisseur.
     *
     * @param int|null $supplierId Identifiant du fournisseur (optionnel).
     * @param string|null $startDate Date de début au format Y-m-d (optionnel).
     * @param string|null $endDate Date de fin au format Y-m-d (optionnel).
     * @return array Rapport des entrées par fournisseur.
     */
    public function getEntriesBySupplier(?int $supplierId = null, ?string $startDate = null, ?string $endDate = null): array
    {
        $query = EntryForm::where('status', 'completed');
        
        // Filtrer par fournisseur si spécifié
        if ($supplierId) {
            $query->where('supplier_id', $supplierId);
        }
        
        // Filtrer par période si spécifiée
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('date', [$start, $end]);
        }
        
        // Récupérer les entrées avec les relations
        $entries = $query->with(['supplier', 'items.product'])->get();
        
        // Regrouper par fournisseur
        $supplierGroups = $entries->groupBy('supplier_id');
        
        $result = [];
        foreach ($supplierGroups as $supId => $supplierEntries) {
            $supplier = $supplierEntries->first()->supplier;
            
            $result[] = [
                'supplier' => [
                    'id' => $supplier->id,
                    'name' => $supplier->name
                ],
                'summary' => [
                    'total_amount' => $supplierEntries->sum('total'),
                    'entries_count' => $supplierEntries->count(),
                    'items_count' => $supplierEntries->flatMap->items->count()
                ],
                'entries' => $supplierEntries
            ];
        }
        
        return $result;
    }

    /**
     * Génère un rapport des entrées par produit.
     *
     * @param int|null $productId Identifiant du produit (optionnel).
     * @param string|null $startDate Date de début au format Y-m-d (optionnel).
     * @param string|null $endDate Date de fin au format Y-m-d (optionnel).
     * @return array Rapport des entrées par produit.
     */
    public function getEntriesByProduct(?int $productId = null, ?string $startDate = null, ?string $endDate = null): array
    {
        // Construire la requête de base pour les items d'entrée
        $query = EntryItem::query()
            ->join('entry_forms', 'entry_items.entry_form_id', '=', 'entry_forms.id')
            ->where('entry_forms.status', 'completed');
            
        // Filtrer par produit si spécifié
        if ($productId) {
            $query->where('entry_items.product_id', $productId);
        }
        
        // Filtrer par période si spécifiée
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('entry_forms.date', [$start, $end]);
        }
        
        // Récupérer les données avec les relations nécessaires
        $items = $query->select('entry_items.*')
            ->with(['product', 'entryForm.supplier'])
            ->get();
        
        // Regrouper par produit
        $productGroups = $items->groupBy('product_id');
        
        $result = [];
        foreach ($productGroups as $prodId => $productItems) {
            $product = $productItems->first()->product;
            
            // Calculer le prix d'achat moyen
            $totalQuantity = $productItems->sum('quantity');
            $totalCost = $productItems->sum(function($item) {
                return $item->quantity * $item->unit_price;
            });
            $averagePrice = $totalQuantity > 0 ? $totalCost / $totalQuantity : 0;
            
            $result[] = [
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku
                ],
                'summary' => [
                    'total_quantity' => $totalQuantity,
                    'total_cost' => $totalCost,
                    'average_price' => $averagePrice,
                    'entries_count' => $productItems->pluck('entry_form_id')->unique()->count()
                ],
                'history' => $productItems->map(function($item) {
                    return [
                        'date' => $item->entryForm->date,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'supplier' => $item->entryForm->supplier->name,
                        'reference' => $item->entryForm->reference
                    ];
                })
            ];
        }
        
        return $result;
    }

    /**
     * Vérifier si la date du bon d'entrée est valide (pas dans le futur).
     *
     * @param string $date La date à vérifier.
     * @return bool True si la date est valide.
     */
    public function isValidDate(string $date): bool
    {
        $entryDate = Carbon::parse($date)->startOfDay();
        $today = Carbon::now()->startOfDay();
        
        return $entryDate->lte($today);
    }
}
