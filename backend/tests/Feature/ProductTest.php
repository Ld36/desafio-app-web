<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testar se um usuário logado pode criar um produto.
     */
    public function test_usuario_autenticado_pode_criar_produto(): void
    {
        // 1. Cria um usuário no banco de testes
        $user = User::factory()->create();

        // 2. Simula que este usuário fez login na API
        Sanctum::actingAs($user);

        // 3. Prepara os dados do produto
        $produtoData = [
            'nome' => 'Notebook Dell',
            'preco' => 4500.50,
            'descricao' => 'Notebook para desenvolvimento',
            'user_id' => $user->id,
        ];

        // 4. Dispara a requisição POST
        $response = $this->postJson('/api/products', $produtoData);

        // 5. Verifica se deu tudo certo (201 Created)
        $response->assertStatus(201)
                 ->assertJsonPath('produto.nome', 'Notebook Dell');

        // 6. Garante que salvou no banco de dados
        $this->assertDatabaseHas('products', [
            'nome' => 'Notebook Dell',
            'preco' => 4500.50
        ]);
    }
}