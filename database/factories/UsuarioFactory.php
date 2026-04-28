<?php

namespace Database\Factories;

use App\Models\Empresa;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Usuario>
 */
class UsuarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'NomeUser' => fake()->name(),
            'EnderecoUser' => fake()->address(),
            'TelefoneUser' => fake()->phoneNumber(),
            'StatusUser' => 1,
            'EmailUser' => fake()->unique()->safeEmail(),
            'SenhaUser' => Hash::make('123456'),
            'TipoUser' => 'admin',
            'DataCadastroUser' => now(),
            'FK_EMPRESA_ID_EMPRESA' => Empresa::factory() // cria empresa automaticamente
        ];
    }
}
