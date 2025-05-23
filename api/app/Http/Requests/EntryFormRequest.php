<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class EntryFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Dans un système réel, vous ajusteriez cette autorisation en fonction des rôles
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */    public function rules(): array
    {
        $entryFormId = $this->route('entry_form');
          return [
            'reference' => 'required|string|max:255|unique:entry_forms,reference' . ($entryFormId ? ',' . $entryFormId : ''),
            'date' => [
                'required',                'date',
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->isAfter(Carbon::now())) {
                        $fail('La date du bon d\'entrée ne peut pas être dans le futur.');
                    }
                },
            ],
            'supplier_id' => 'required|exists:suppliers,id',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|string|in:draft,pending,completed,cancelled',
            'total' => 'nullable|numeric|min:0',
            'user_id' => 'required|exists:users,id',
            'items' => 'required_unless:status,cancelled|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0.01',
            'items.*.total' => 'nullable|numeric|min:0',
            'cancel_reason' => 'nullable|string|max:500|required_if:status,cancelled',
            'validation_note' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'reference.required' => 'La référence est obligatoire.',
            'reference.string' => 'La référence doit être une chaîne de caractères.',
            'reference.max' => 'La référence ne doit pas dépasser 255 caractères.',
            'reference.unique' => 'Cette référence est déjà utilisée par un autre bon d\'entrée.',
            'date.required' => 'La date est obligatoire.',
            'date.date' => 'Veuillez saisir une date valide.',
            'supplier_id.required' => 'Le fournisseur est obligatoire.',
            'supplier_id.exists' => 'Le fournisseur sélectionné n\'existe pas.',
            'status.required' => 'Le statut est obligatoire.',
            'status.string' => 'Le statut doit être une chaîne de caractères.',
            'status.in' => 'Le statut doit être draft, pending ou completed.',
            'total.required' => 'Le total est obligatoire.',
            'total.numeric' => 'Le total doit être un nombre.',
            'total.min' => 'Le total doit être supérieur ou égal à 0.',
            'user_id.required' => 'L\'utilisateur est obligatoire.',
            'user_id.exists' => 'L\'utilisateur sélectionné n\'existe pas.',
        ];
    }
}
