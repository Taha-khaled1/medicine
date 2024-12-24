<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'quantity',
        'expiry_date',
        'type',
        'subunits_per_unit',
        'subunits_count',
        'scientific_form'
    ];

    protected $casts = [
        'expiry_date' => 'date'
    ];
}
