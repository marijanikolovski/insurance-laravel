<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Age extends Model
{
    use HasFactory;

    protected $fillable = [
        'age',
        'value',
    ];

    public function insurances()
    {
        return $this->hasMany(Insurance::class);
    }
}
