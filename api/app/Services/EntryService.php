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

    
    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }
    
    
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
    }    
    public function validate(EntryForm $entryForm, ?string $validationNote = null): EntryForm
    {
        // Vérifier si le bon est en statut draft
        Log::info("Attempting to validate entry form ID: {$entryForm->id} with current status: {$entryForm->status}");
        if ($entryForm->status !== 'draft' && $entryForm->status !== 'pending') {
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
                // Note: Stock quantities are no longer updated during validation
                // as per user requirement. Only status change is performed.
                foreach ($entryForm->entryItems as $item) {
                    $product = Product::findOrFail($item->product_id);
                    
                    // Log the validation without updating stock
                    Log::info("Bon d'entrée #{$entryForm->reference} validé pour le produit {$product->name} - Quantité: {$item->quantity} (Stock non modifié)");
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
    }    
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

    
    public function cancel(EntryForm $entryForm, ?string $reason = null): EntryForm
    {
        // Vérifier si le bon peut être annulé
        if ($entryForm->status === 'cancelled') {
            throw new Exception("Ce bon d'entrée est déjà annulé.");
        }

        $oldStatus = $entryForm->status;

        // Transaction pour assurer l'intégrité des données
        return DB::transaction(function () use ($entryForm, $oldStatus, $reason) {            try {
                // Stock impact is no longer reversed during cancellation
                // as per user requirement. Only status change is performed.
                if ($oldStatus === 'completed') {
                    Log::info("Annulation du bon d'entrée #{$entryForm->reference} - Stock non modifié comme demandé");
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

    
    public function isValidDate(string $date): bool
    {
        $entryDate = Carbon::parse($date)->startOfDay();
        $today = Carbon::now()->startOfDay();
        
        return $entryDate->lte($today);
    }
}
