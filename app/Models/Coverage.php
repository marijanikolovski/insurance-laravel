<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coverage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
        'value_user_over30',
        'description'
    ];

    public function insurances()
    {
        return $this->hasMany(Insurance::class);
    }
}
