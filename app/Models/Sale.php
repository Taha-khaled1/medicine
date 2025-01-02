<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Sale extends Model
{
    protected $fillable = [
        'medicine_id',
        'shift_id',
        'box_count',
        'strip_count',
        'total_price',
        'is_paid'
    ];

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
