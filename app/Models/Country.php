<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    public $table = 'countries';
    public $timestamps = false;
    protected $guarded = [];
    public function cities()
    {
        return $this->hasMany(City::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function learningPath()
    {
        return $this->belongsTo(LearningPath::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
