<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Listar os usuários com Filtros e Paginação
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('nome')) {
            $query->where('nome', 'ilike', '%' . $request->nome . '%');
        }

        if ($request->filled('cpf')) {
            $query->where('cpf', 'ilike', '%' . $request->cpf . '%');
        }

        $users = $query->paginate(10);
        return response()->json($users, 200);
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'nome' => $request->nome,
            'cpf' => $request->cpf,
            'email' => $request->email,
            'password' => Hash::make($request->senha), 
        ]);

        return response()->json(['message' => 'Usuário criado com sucesso!', 'user' => $user], 201);
    }

    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Usuário não encontrado'], 404);
        
        return response()->json($user, 200);
    }

    /**
     * Atualizar um usuário
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Usuário não encontrado'], 404);

        $data = $request->only(['nome', 'cpf', 'email']);

        if ($request->filled('senha')) {
            $data['password'] = Hash::make($request->senha);
        }

        $user->update($data);

        return response()->json(['message' => 'Usuário atualizado com sucesso!', 'user' => $user], 200);
    }

    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'Usuário não encontrado'], 404);

        $user->delete();
        return response()->json(['message' => 'Usuário excluído com sucesso!'], 200);
    }
}