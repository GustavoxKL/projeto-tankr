<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar super admin
        Usuario::create([
            'empresa_id' => null,
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'nome' => 'Administrador',
            'email' => 'admin@sistema.com',
            'role' => 'admin',
            'tipo' => 'superadmin',
            'ativo' => true
        ]);
        
        echo "✅ Super admin criado!\n";
        echo "   Username: admin\n";
        echo "   Senha: admin123\n";
    }
}
