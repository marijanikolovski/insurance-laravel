<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgeRequest;
use App\Models\Age;
use Illuminate\Http\Request;

class AgesController extends Controller
{
    public function index()
    {
        $ages = Age::all();

        return response()->json($ages);
    }

    public function store(AgeRequest $request)
    {
        $validated = $request->validated();

        $age = Age::create($validated);

        return response()->json($age, 201);
    }
}
