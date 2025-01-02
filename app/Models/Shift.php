<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = [
        'start_time',
        'end_time',
        'shift_date',
        'status',
        'initial_amount',
        'remaining_amount',
        'unpaid_amount',
        'total_amount',
        'actual_amount'
    ];

    protected $casts = [
        'shift_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
