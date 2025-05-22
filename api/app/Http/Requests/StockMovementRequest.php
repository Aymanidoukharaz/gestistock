<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockMovementRequest extends FormRequest
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
    {        return [
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:entry,exit',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string',
            'date' => 'required|date',
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
            'product_id.required' => 'Le produit est obligatoire.',
            'product_id.exists' => 'Le produit sélectionné n\'existe pas.',
            'type.required' => 'Le type de mouvement est obligatoire.',
            'type.in' => 'Le type doit être soit "entry" soit "exit".',
            'quantity.required' => 'La quantité est obligatoire.',
            'quantity.integer' => 'La quantité doit être un nombre entier.',
            'quantity.min' => 'La quantité doit être supérieure ou égale à 1.',
            'date.required' => 'La date est obligatoire.',
            'date.date' => 'Veuillez saisir une date valide.',
            'user_id.required' => 'L\'utilisateur est obligatoire.',
            'user_id.exists' => 'L\'utilisateur sélectionné n\'existe pas.',
        ];
    }
}
