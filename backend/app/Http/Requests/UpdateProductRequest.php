<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => 'sometimes|required|string|max:255',
            'preco' => 'sometimes|required|numeric|min:0.01',
            'descricao' => 'nullable|string',
            'user_id' => 'sometimes|required|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'preco.numeric' => 'O preço deve ser um número válido.',
            'preco.min' => 'O preço deve ser maior que zero.',
            'user_id.exists' => 'O usuário informado não existe no sistema.',
        ];
    }
}