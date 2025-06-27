<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => [Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()],
            'confirmed'
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'E aí, não esquece de colocar seu nome!',
            'name.string'   => 'Opa, o nome precisa ser texto, viu?',
            'name.max'      => 'Calma lá! Nome com mais de 255 caracteres não rola.',
            'email.required' => 'Precisamos saber seu e-mail!',
            'email.email' => 'Você precisa enviar um e-mail no formato correto!',
            'email.unique' => 'Pera lá, parece que esse e-mail já tem feito viagens por aqui!',
            'password.min'       => 'Sua senha deve ter ao menos 8 caracteres.',
            'password.letters'   => 'Sua senha deve conter pelo menos uma letra.',
            'password.mixedCases' => 'Sua senha deve conter letras maiúsculas e minúsculas.',
            'password.numbers'   => 'Sua senha deve conter pelo menos um número.',
            'password.symbols'   => 'Sua senha deve conter pelo menos um símbolo.',
            'password.confirmed' => 'A confirmação da senha não coincide.',
        ];
    }
}
