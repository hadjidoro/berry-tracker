<?php

namespace Database\Factories;

use App\Enums\Month;
use App\Models\Account;
use App\Models\Category;
use App\Models\Operation;
use App\Enums\OperationType;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class OperationFactory extends Factory
{
    protected $model = Operation::class;

    public function definition(): array
    {
        return [
            'category_id'  => Category::factory(),
            'type'         => fake()->randomElement(OperationType::cases()),
            'amount'       => fake()->randomFloat(),
            'fees'         => fake()->randomFloat(),
            'description'  => fake()->words(asText: true),
            'month'        => fake()->randomElement(Month::cases()),
            'performed_at' => Carbon::now(),
            'created_at'   => Carbon::now(),
            'updated_at'   => Carbon::now(),
            'deleted_at'   => Carbon::now(),
            'account_id'   => Account::factory(),
        ];
    }
}
