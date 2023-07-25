<?php

namespace App\Http\Controllers;

use App\Http\Requests\CityRequest;
use App\Models\City;
use Illuminate\Http\Request;

class CitiesController extends Controller
{
    public function index()
    {
        $cities = City::all();

        return response()->json($cities);
    }

    public function store(CityRequest $request)
    {
        $validated = $request->validated();

        $city = City::create($validated);

        return response()->json($city, 201);
    }
}
