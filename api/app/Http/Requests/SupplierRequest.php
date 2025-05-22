<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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
        $supplierId = $this->route('supplier');
        
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:suppliers,email' . ($supplierId ? ',' . $supplierId : ''),
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
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
            'name.required' => 'Le nom du fournisseur est obligatoire.',
            'name.string' => 'Le nom doit être une chaîne de caractères.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'email.max' => 'L\'email ne doit pas dépasser 255 caractères.',
            'email.unique' => 'Cette adresse email est déjà utilisée par un autre fournisseur.',
            'phone.max' => 'Le numéro de téléphone ne doit pas dépasser 50 caractères.',
            'address.max' => 'L\'adresse ne doit pas dépasser 255 caractères.',
        ];
    }
}
