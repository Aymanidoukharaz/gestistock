<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Seuls les admin peuvent gérer les utilisateurs, mais l'autorisation est déjà gérée par le middleware
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'role' => 'required|string|in:admin,magasinier',
            'active' => 'sometimes|boolean',
        ];
        
        // Pour la création d'utilisateur
        if ($this->isMethod('post')) {
            $rules['password'] = 'required|string|min:6';
            $rules['email'] = 'required|string|email|max:255|unique:users';
        }
        
        // Pour la mise à jour d'utilisateur
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $userId = $this->route('id');
            $rules['password'] = 'sometimes|nullable|string|min:6';
            $rules['email'] = 'sometimes|required|string|email|max:255|unique:users,email,' . $userId;
            
            // Validation additionnelle pour empêcher le changement de rôle de son propre compte admin
            if ($userId == auth()->id() && $this->input('role') !== 'admin') {
                $rules['role'] = 'in:admin';
            }
        }
        
        return $rules;
    }
    
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Cette adresse email est déjà utilisée par un autre utilisateur.',
            'role.in' => 'Le rôle doit être soit admin, soit magasinier.',
            'role.in:admin' => 'Vous ne pouvez pas changer votre propre rôle d\'administrateur.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
        ];
    }
}
