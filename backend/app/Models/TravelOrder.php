<?php

namespace App\Models;

use App\Enum\TravelOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelOrder extends Model
{
    protected $casts = [
        'status' => TravelOrderStatus::class
    ];
    protected $fillable = [
        'requester_name',
        'destination',
        'departure_date',
        'return_date',
        'user_id',
        'status'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
