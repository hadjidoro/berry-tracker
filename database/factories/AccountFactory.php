<?php

namespace Database\Factories;

use App\Models\Account;
use App\Enums\AccountType;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'name'       => fake()->name(),
            'type'       => fake()->randomElement(AccountType::cases()),
            'balance'    => fake()->randomFloat(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'deleted_at' => Carbon::now(),
        ];
    }
}
