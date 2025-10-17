<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvaluationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Seuls les patients peuvent créer des évaluations
        return auth()->check() && auth()->user()->role === 'patient';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'evaluated_user_id' => 'required|exists:users,id',
            'type_evaluation' => 'required|in:medecin,infirmier',
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000',
            'consultation_id' => 'nullable|exists:consultations,id'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'evaluated_user_id.required' => 'Veuillez sélectionner un professionnel à évaluer.',
            'evaluated_user_id.exists' => 'Le professionnel sélectionné n\'existe pas.',
            'type_evaluation.required' => 'Le type d\'évaluation est obligatoire.',
            'type_evaluation.in' => 'Le type d\'évaluation doit être "medecin" ou "infirmier".',
            'note.required' => 'La note est obligatoire.',
            'note.integer' => 'La note doit être un nombre entier.',
            'note.min' => 'La note minimale est de 1 étoile.',
            'note.max' => 'La note maximale est de 5 étoiles.',
            'commentaire.max' => 'Le commentaire ne peut pas dépasser 1000 caractères.',
            'consultation_id.exists' => 'La consultation sélectionnée n\'existe pas.'
        ];
    }
}