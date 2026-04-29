<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testar se um usuário pode ser criado com dados válidos.
     */
    public function test_pode_criar_usuario_com_dados_validos(): void
    {
        $userData = [
            'nome' => 'Joao Silva',
            'cpf' => '123.456.789-00',
            'email' => 'joao@teste.com',
            'senha' => '123456'
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(201)
                 ->assertJsonPath('user.nome', 'Joao Silva');
        
        $this->assertDatabaseHas('users', ['email' => 'joao@teste.com']);
    }

    /**
     * Testar se a validação de CPF único está funcionando.
     */
    public function test_nao_pode_criar_usuario_com_cpf_duplicado(): void
    {
        // Criar um usuário no banco primeiro
        User::factory()->create(['cpf' => '111.111.111-11']);

        $userData = [
            'nome' => 'Outro Usuario',
            'cpf' => '111.111.111-11', // CPF igual ao anterior
            'email' => 'outro@teste.com',
            'senha' => '123456'
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['cpf']);
    }

    /**
     * Testar se rotas protegidas barram usuários sem token.
     */
    public function test_rotas_protegidas_exigem_autenticacao(): void
    {
        $response = $this->getJson('/api/relatorio-sql');

        $response->assertStatus(401);
    }
}