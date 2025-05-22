<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Contracts\Validation\Validator;

class ProductRequest extends FormRequest
{
    /**
     * The validator instance.
     *
     * @var \Illuminate\Contracts\Validation\Validator
     */
    protected $validator;

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
        $productId = $this->route('product');
        
        return [
            'reference' => 'required|string|max:255|unique:products,reference' . ($productId ? ',' . $productId : ''),
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
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
            'reference.required' => 'La référence du produit est obligatoire.',
            'reference.unique' => 'Cette référence est déjà utilisée par un autre produit.',
            'reference.max' => 'La référence ne doit pas dépasser 255 caractères.',
            'name.required' => 'Le nom du produit est obligatoire.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'category_id.required' => 'La catégorie est obligatoire.',
            'category_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'price.required' => 'Le prix est obligatoire.',
            'price.numeric' => 'Le prix doit être un nombre.',
            'price.min' => 'Le prix doit être supérieur ou égal à 0.',
            'quantity.required' => 'La quantité est obligatoire.',
            'quantity.integer' => 'La quantité doit être un nombre entier.',
            'quantity.min' => 'La quantité doit être supérieure ou égale à 0.',
            'min_stock.required' => 'Le stock minimum est obligatoire.',
            'min_stock.integer' => 'Le stock minimum doit être un nombre entier.',
            'min_stock.min' => 'Le stock minimum doit être supérieur ou égal à 0.',
        ];
    }
}
