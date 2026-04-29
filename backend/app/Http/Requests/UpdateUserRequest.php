<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'nome' => 'sometimes|required|string|max:255',
            'cpf' => ['sometimes', 'required', 'string', 'regex:/^\d{3}\.\d{3}\.\d{3}\-\d{2}$/', 'unique:users,cpf,' . $userId],
            'email' => 'sometimes|required|email|unique:users,email,' . $userId,
            'senha' => 'nullable|string|min:6', 
        ];
    }

    public function messages(): array
    {
        return [
            'cpf.regex' => 'O formato do CPF é inválido. Use 000.000.000-00.',
            'cpf.unique' => 'Este CPF já está cadastrado em outro usuário.',
            'email.unique' => 'Este e-mail já está em uso por outro usuário.',
            'senha.min' => 'A senha deve conter no mínimo 6 caracteres.',
        ];
    }
}