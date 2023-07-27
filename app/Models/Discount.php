<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
        'description'
    ];

    public function insurances()
    {
        return $this->hasMany(Insurance::class);
    }
}
