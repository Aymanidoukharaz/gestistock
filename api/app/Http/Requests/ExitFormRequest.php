<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExitFormRequest extends FormRequest
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
        $exitFormId = $this->route('exit_form');
          return [
            'reference' => 'required|string|max:255|unique:exit_forms,reference' . ($exitFormId ? ',' . $exitFormId : ''),
            'date' => 'required|date',
            'destination' => 'required|string|max:255',
            'reason' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|string|in:draft,pending,completed',
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
            'reference.unique' => 'Cette référence est déjà utilisée par un autre bon de sortie.',
            'date.required' => 'La date est obligatoire.',
            'date.date' => 'Veuillez saisir une date valide.',
            'destination.required' => 'La destination est obligatoire.',
            'destination.string' => 'La destination doit être une chaîne de caractères.',
            'destination.max' => 'La destination ne doit pas dépasser 255 caractères.',
            'status.required' => 'Le statut est obligatoire.',
            'status.string' => 'Le statut doit être une chaîne de caractères.',
            'status.in' => 'Le statut doit être draft, pending ou completed.',
            'user_id.required' => 'L\'utilisateur est obligatoire.',
            'user_id.exists' => 'L\'utilisateur sélectionné n\'existe pas.',
        ];
    }
}
