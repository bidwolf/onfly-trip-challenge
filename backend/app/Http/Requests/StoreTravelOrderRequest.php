<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTravelOrderRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // I will use gates and policies here
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'requester_name' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'departure_date' => 'required|date_format:Y-m-d|after:today',
            'return_date' => 'required|date_format:Y-m-d|after:departure_date'
        ];
    }

    public function messages()
    {
        return [
            'requester_name.required'   => 'O nome do solicitante é obrigatório.',
            'requester_name.string'     => 'O nome do solicitante deve ser um texto.',
            'requester_name.max'        => 'O nome do solicitante não pode ter mais que 255 caracteres.',

            'destination.required'      => 'O destino é obrigatório.',
            'destination.string'        => 'O destino deve ser um texto.',
            'destination.max'           => 'O destino não pode ter mais que 255 caracteres.',

            'departure_date.required'   => 'A data de partida é obrigatória.',
            'departure_date.date_format' => 'A data de partida deve ser uma data válida e estar no formato AAAA-MM-DD.',
            'departure_date.after'      => 'A data de partida deve ser uma data futura.',

            'return_date.required'      => 'A data de retorno é obrigatória.',
            'return_date.date_format'   => 'A data de retorno deve ser uma data válida e estar no formato AAAA-MM-DD.',
            'return_date.after'        => 'A data de retorno deve ser posterior à data de partida.',
        ];
    }
}
