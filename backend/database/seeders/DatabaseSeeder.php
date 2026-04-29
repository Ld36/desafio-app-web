<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Cria um usuário Administrador padrão para testes
        \App\Models\User::factory()->create([
            'nome' => 'Administrador',
            'cpf' => '000.000.000-00',
            'email' => 'admin@admin.com',
            'password' => bcrypt('01010101'),
        ]);
    }
}
