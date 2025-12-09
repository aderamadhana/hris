<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    public function definition(): array
    {
        return [
            // id sengaja TIDAK diacak → biar bisa dikontrol di seeder
            'role_name' => 'EMPLOYEE',
        ];
    }

    /**
     * State khusus untuk tiap role
     */
    public function admin(): static
    {
        return $this->state(fn () => [
            'role_name' => 'admin',
        ]);
    }

    public function employee(): static
    {
        return $this->state(fn () => [
            'role_name' => 'employee',
        ]);
    }
}
