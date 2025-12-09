<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $gender = $this->faker->randomElement(['Laki-laki', 'Perempuan']);

        return [
            'nrp' => 'NRP-' . $this->faker->unique()->numerify('#####'),
            'user_id' => null, // di-bind manual kalau perlu

            'nama' => $this->faker->name(
                $gender === 'Laki-laki' ? 'male' : 'female'
            ),

            'jenis_kelamin' => $gender,
            'tempat_lahir' => $this->faker->city,
            'tanggal_lahir' => $this->faker->dateTimeBetween('-45 years', '-18 years')->format('Y-m-d'),

            'agama' => $this->faker->randomElement([
                'Islam',
                'Kristen',
                'Katolik',
                'Hindu',
                'Buddha',
            ]),

            'status_perkawinan' => $this->faker->randomElement([
                'Belum Kawin',
                'Kawin',
            ]),

            'kewarganegaraan' => 'Indonesia',

            'status_active' => '1',
        ];
    }

    /**
     * Employee aktif
     */
    public function active(): static
    {
        return $this->state(fn () => [
            'status_active' => '1',
        ]);
    }

    /**
     * Employee non-aktif
     */
    public function inactive(): static
    {
        return $this->state(fn () => [
            'status_active' => '0',
        ]);
    }

    /**
     * Employee + User (relasi otomatis)
     */
    public function withUser(): static
    {
        return $this->state(fn () => [
            'user_id' => User::factory(),
        ]);
    }
}
