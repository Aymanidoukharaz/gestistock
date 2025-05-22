<?php

namespace App\Http\Requests;

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
     */
    public function rules(): array
    {
        $entryFormId = $this->route('entry_form');
          return [
            'reference' => 'required|string|max:255|unique:entry_forms,reference' . ($entryFormId ? ',' . $entryFormId : ''),
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:draft,pending,completed',
            'total' => 'required|numeric|min:0',
            'user_id' => 'required|exists:users,id',
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
