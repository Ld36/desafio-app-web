<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Listar os produtos (com filtro opcional por usuário)
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Requisito do desafio: visualizar produtos de um usuário específico
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Carrega o usuário atrelado ao produto para o Front-end ter os dados completos
        $produtos = $query->with('user')->paginate(10);
        
        return response()->json($produtos, 200);
    }

    public function store(StoreProductRequest $request)
    {
        $produto = Product::create($request->validated());

        return response()->json([
            'message' => 'Produto criado com sucesso!',
            'produto' => $produto
        ], 201);
    }

    public function show(string $id)
    {
        // with('user') traz os dados do usuário junto com o produto
        $produto = Product::with('user')->find($id);

        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }

        return response()->json($produto, 200);
    }

    public function update(UpdateProductRequest $request, string $id)
    {
        $produto = Product::find($id);

        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }

        $produto->update($request->validated());

        return response()->json([
            'message' => 'Produto atualizado com sucesso!',
            'produto' => $produto
        ], 200);
    }

    public function destroy(string $id)
    {
        $produto = Product::find($id);

        if (!$produto) {
            return response()->json(['message' => 'Produto não encontrado'], 404);
        }

        $produto->delete();

        return response()->json(['message' => 'Produto excluído com sucesso!'], 200);
    }
}