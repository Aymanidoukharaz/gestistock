<?php

namespace App\Services;

use App\Models\ExitForm;
use App\Models\ExitFormHistory;
use App\Models\ExitItem;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExitService
{
    protected $stockService;

    
    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }    
    private function isValidDate($date): bool
    {
        $checkDate = $date instanceof Carbon ? $date : Carbon::parse($date);
        return $checkDate->startOfDay()->lte(Carbon::now()->startOfDay());
    }

    
    public function validate(ExitForm $exitForm, ?string $validationNote = null): ExitForm
    {
        // Vérifier si le bon est en statut draft
        if ($exitForm->status !== 'draft') {
            throw new Exception("Le bon de sortie doit être en statut 'draft' pour être validé.");
        }
        
        // Vérifier que le bon contient au moins un item
        if ($exitForm->items()->count() === 0) {
            throw new Exception("Impossible de valider un bon de sortie sans articles.");
        }
        
        // Vérifier que la date n'est pas dans le futur
        if (!$this->isValidDate($exitForm->date)) {
            throw new Exception("La date du bon de sortie ne peut pas être dans le futur.");
        }

        // Transaction pour assurer l'intégrité des données
        return DB::transaction(function () use ($exitForm, $validationNote) {
            try {
                // Étape 1: Enregistrer l'état initial dans l'historique
                $this->logHistory(
                    $exitForm,
                    'status',
                    $exitForm->status,
                    'pending',
                    $validationNote ?? "Début du processus de validation"
                );                // Étape 2: Vérifier la disponibilité du stock pour tous les produits
                // (checking is kept but stock quantities won't be updated)
                foreach ($exitForm->exitItems as $item) {
                    $product = Product::findOrFail($item->product_id);
                    
                    if (!$this->stockService->hasEnoughStock($product, $item->quantity)) {
                        throw new Exception(
                            "Stock insuffisant pour le produit {$product->name}. " .
                            "Disponible: {$product->quantity}, Demandé: {$item->quantity}"
                        );
                    }
                }
                
                // Étape 3: Changer le statut en 'pending'
                $exitForm->status = 'pending';
                $exitForm->save();
                  // Étape 4: Traiter chaque ligne du bon de sortie
                // Note: Stock quantities are no longer updated during validation
                // as per user requirement. Only status change is performed.
                foreach ($exitForm->exitItems as $item) {
                    $product = Product::findOrFail($item->product_id);
                    
                    // Log the validation without updating stock
                    Log::info("Bon de sortie #{$exitForm->reference} validé pour le produit {$product->name} - Quantité: {$item->quantity} (Stock non modifié)");
                }

                // Étape 5: Changer le statut en 'completed' et enregistrer dans l'historique
                $this->logHistory(
                    $exitForm,
                    'status',
                    'pending',
                    'completed',
                    $validationNote ?? "Fin du processus de validation"
                );
                
                $exitForm->status = 'completed';
                $exitForm->save();

                Log::info("Bon de sortie #{$exitForm->reference} validé avec succès");
                return $exitForm;
            } catch (Exception $e) {
                Log::error("Erreur lors de la validation du bon de sortie #{$exitForm->reference}: " . $e->getMessage());
                throw $e;
            }
        });
    }

    
    public function logHistory(
        ExitForm $exitForm,
        string $fieldName,
        $oldValue,
        $newValue,
        ?string $reason = null
    ): ExitFormHistory {
        return ExitFormHistory::create([
            'exit_form_id' => $exitForm->id,
            'user_id' => Auth::id() ?? $exitForm->user_id,
            'field_name' => $fieldName,
            'old_value' => is_array($oldValue) ? json_encode($oldValue) : $oldValue,
            'new_value' => is_array($newValue) ? json_encode($newValue) : $newValue,
            'change_reason' => $reason,
        ]);
    }

    
    public function detectDuplicates(array $data): Collection
    {
        $query = ExitForm::query();

        // Vérifier uniquement la correspondance sur la référence
        if (isset($data['reference'])) {
            $query->where('reference', $data['reference']);
        } else {
            // Si pas de référence spécifiée, aucun doublon possible
            return collect();
        }

        // Exclure le bon de sortie en cours d'édition
        if (isset($data['id'])) {
            $query->where('id', '!=', $data['id']);
        }

        // Exclure les bons annulés
        $query->where('status', '!=', 'cancelled');

        return $query->with(['user', 'items.product'])->get();
    }

    
    public function cancel(ExitForm $exitForm, ?string $reason = null): ExitForm
    {
        // Vérifier si le bon peut être annulé
        if ($exitForm->status === 'cancelled') {
            throw new Exception("Ce bon de sortie est déjà annulé.");
        }

        $oldStatus = $exitForm->status;

        // Transaction pour assurer l'intégrité des données
        return DB::transaction(function () use ($exitForm, $oldStatus, $reason) {            try {
                // Stock impact is no longer reversed during cancellation
                // as per user requirement. Only status change is performed.
                if ($oldStatus === 'completed') {
                    Log::info("Annulation du bon de sortie #{$exitForm->reference} - Stock non modifié comme demandé");
                }

                // Changer le statut en 'cancelled'
                $exitForm->status = 'cancelled';
                $exitForm->save();

                // Ajouter à l'historique
                $this->logHistory(
                    $exitForm,
                    'status',
                    $oldStatus,
                    'cancelled',
                    $reason
                );

                Log::info("Bon de sortie #{$exitForm->reference} annulé avec succès. Raison: " . ($reason ?? 'Non spécifiée'));
                return $exitForm;
            } catch (Exception $e) {
                Log::error("Erreur lors de l'annulation du bon de sortie #{$exitForm->reference}: " . $e->getMessage());
                throw $e;
            }
        });
    }

    
    public function getExitsByPeriod(string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();
        
        $exits = ExitForm::whereBetween('date', [$start, $end])
            ->where('status', 'completed')
            ->with(['user', 'items.product'])
            ->get();
            
        $exitCount = $exits->count();
        $itemsCount = $exits->flatMap->items->count();
        $totalQuantity = $exits->flatMap->items->sum('quantity');
        
        return [
            'period' => [
                'start' => $startDate,
                'end' => $endDate
            ],
            'summary' => [
                'exits_count' => $exitCount,
                'items_count' => $itemsCount,
                'total_quantity' => $totalQuantity
            ],
            'exits' => $exits->map(function ($exit) {
                return [
                    'id' => $exit->id,
                    'reference' => $exit->reference,
                    'date' => $exit->date->format('Y-m-d'),
                    'destination' => $exit->destination,
                    'reason' => $exit->reason,
                    'user' => $exit->user->name,
                    'items_count' => $exit->items->count(),
                    'total_quantity' => $exit->items->sum('quantity')
                ];
            })
        ];
    }

    
    public function getExitsByDestination(?string $startDate = null, ?string $endDate = null): array
    {
        $query = ExitForm::where('status', 'completed');
        
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('date', [$start, $end]);
        }
        
        $exits = $query->with(['items.product'])->get();
        
        $destinations = $exits->groupBy('destination')->map(function ($groupedExits, $destination) {
            $totalQuantity = $groupedExits->flatMap->items->sum('quantity');
            $itemsCount = $groupedExits->flatMap->items->count();
            
            return [
                'destination' => $destination,
                'exits_count' => $groupedExits->count(),
                'items_count' => $itemsCount,
                'total_quantity' => $totalQuantity
            ];
        })->values()->sortByDesc('total_quantity');
        
        return [
            'period' => $startDate && $endDate ? [
                'start' => $startDate,
                'end' => $endDate
            ] : 'all',
            'destinations' => $destinations
        ];
    }
}
