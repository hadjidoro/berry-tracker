<?php

namespace App\Models;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'type' => AccountType::class,
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($account) {
            $account->balance = 0;
        });
    }

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }
}
