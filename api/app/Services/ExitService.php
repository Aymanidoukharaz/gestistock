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

    /**
     * Constructeur du service.
     *
     * @param StockService $stockService
     */
    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }    /**
     * Vérifie si une date est valide (pas dans le futur)
     *
     * @param string|Carbon $date La date à vérifier
     * @return bool True si la date est valide
     */
    private function isValidDate($date): bool
    {
        $checkDate = $date instanceof Carbon ? $date : Carbon::parse($date);
        return $checkDate->startOfDay()->lte(Carbon::now()->startOfDay());
    }

    /**
     * Valider un bon de sortie et mettre à jour le stock.
     *
     * @param ExitForm $exitForm Bon de sortie à valider.
     * @param string|null $validationNote Note optionnelle sur la validation
     * @return ExitForm Le bon de sortie validé.
     * @throws Exception Si le bon de sortie n'est pas en statut draft, 
     *                   si le stock est insuffisant ou en cas d'erreur.
     */
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
                );
                
                // Étape 2: Vérifier la disponibilité du stock pour tous les produits
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
                foreach ($exitForm->exitItems as $item) {
                    $product = Product::findOrFail($item->product_id);
                    
                    // Soustraire la quantité du stock (quantité négative)
                    $this->stockService->updateStock($product, -$item->quantity);
                    
                    // Créer un mouvement de stock pour cette sortie
                    $this->stockService->createStockMovement(
                        $product,
                        'exit',
                        $item->quantity,
                        "Sortie de stock via bon #{$exitForm->reference}"
                    );
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

    /**
     * Crée un enregistrement dans l'historique des modifications du bon de sortie.
     *
     * @param ExitForm $exitForm Le bon de sortie concerné.
     * @param string $fieldName Le nom du champ modifié.
     * @param mixed $oldValue L'ancienne valeur.
     * @param mixed $newValue La nouvelle valeur.
     * @param string|null $reason La raison de la modification.
     * @return ExitFormHistory L'historique créé.
     */
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

    /**
     * Détecte les doublons potentiels pour un bon de sortie.
     * Un doublon est défini uniquement par la référence identique.
     *
     * @param array $data Les données du bon de sortie à vérifier.
     * @return Collection Les bons de sortie potentiellement en doublon.
     */
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

    /**
     * Annule un bon de sortie et ajuste le stock si nécessaire.
     *
     * @param ExitForm $exitForm Le bon de sortie à annuler.
     * @param string|null $reason La raison de l'annulation.
     * @return ExitForm Le bon de sortie annulé.
     * @throws Exception Si le bon de sortie ne peut pas être annulé.
     */
    public function cancel(ExitForm $exitForm, ?string $reason = null): ExitForm
    {
        // Vérifier si le bon peut être annulé
        if ($exitForm->status === 'cancelled') {
            throw new Exception("Ce bon de sortie est déjà annulé.");
        }

        $oldStatus = $exitForm->status;

        // Transaction pour assurer l'intégrité des données
        return DB::transaction(function () use ($exitForm, $oldStatus, $reason) {
            try {
                // Si le bon était en statut 'completed', il faut annuler l'impact sur le stock
                if ($oldStatus === 'completed') {
                    foreach ($exitForm->exitItems as $item) {
                        $product = Product::findOrFail($item->product_id);
                        
                        // Annuler l'impact sur le stock (quantité positive pour augmenter)
                        $this->stockService->updateStock($product, $item->quantity);
                        
                        // Créer un mouvement de stock pour cette annulation
                        $this->stockService->createStockMovement(
                            $product,
                            'entry',
                            $item->quantity,
                            "Annulation du bon de sortie #{$exitForm->reference}"
                        );
                    }
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

    /**
     * Génère un rapport des sorties par période.
     *
     * @param string $startDate Date de début au format Y-m-d.
     * @param string $endDate Date de fin au format Y-m-d.
     * @return array Rapport des sorties par période.
     */
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

    /**
     * Génère un rapport des sorties par destination.
     *
     * @param string|null $startDate Date de début optionnelle au format Y-m-d.
     * @param string|null $endDate Date de fin optionnelle au format Y-m-d.
     * @return array Rapport des sorties par destination.
     */
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
