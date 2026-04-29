<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'nome' => 'required|string|max:255',
            'cpf' => ['required', 'string', 'regex:/^\d{3}\.\d{3}\.\d{3}\-\d{2}$/', 'unique:users,cpf'],
            'email' => 'required|email|unique:users,email',
            'senha' => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O campo nome é obrigatório.',
            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.regex' => 'O formato do CPF é inválido. Use 000.000.000-00.',
            'cpf.unique' => 'Este CPF já está cadastrado em nosso sistema.',
            'email.required' => 'O campo e-mail é obrigatório.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso.',
            'senha.required' => 'A senha é obrigatória.',
            'senha.min' => 'A senha deve conter no mínimo 6 caracteres.',
        ];
    }
}