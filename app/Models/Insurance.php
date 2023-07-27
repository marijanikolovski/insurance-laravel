<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'age_id',
        'city_id',
        'vehicle_power',
        'voucher',
        'price_match',
        'discount_id',
    ];

    public function age()
    {
        return $this->belongsTo(Age::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function discoutns()
    {
        return $this->hasMany(Discount::class);
    }

    public function coverages()
    {
        return $this->hasMany(Discount::class);
    }
}
