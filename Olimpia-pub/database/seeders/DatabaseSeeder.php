<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Inserta los datos iniciales de la aplicación.
     */
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            UsuarioSeeder::class,
            CategoriaSeeder::class,
            MesaSeeder::class,
            ProductoSeeder::class,
            ContenidoInicioSeeder::class,
        ]);
    }
}
