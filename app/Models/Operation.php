<?php

namespace App\Models;

use Carbon\Month;
use App\Enums\OperationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Operation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::created(function ($operation) {
            match ($operation->type) {
                OperationType::Deposit    => $operation->account->increment('balance', $operation->amount - $operation->fees),
                OperationType::Withdrawal => $operation->account->decrement('balance', $operation->amount + $operation->fees),
            };
        });
    }

    protected $casts = [
        'month'        => Month::class,
        'type'         => OperationType::class,
        'performed_at' => 'date',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
