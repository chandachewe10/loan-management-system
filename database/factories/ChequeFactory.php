<?php

namespace Database\Factories;

use App\Models\Cheque;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChequeFactory extends Factory
{
    protected $model = Cheque::class;

    public function definition()
    {
        return [
            'check_number' => $this->faker->unique()->numerify('CHK###'),
            'amount' => $this->faker->randomFloat(2, 100, 10000),
            'beneficiary' => $this->faker->name,
        ];
    }
}
