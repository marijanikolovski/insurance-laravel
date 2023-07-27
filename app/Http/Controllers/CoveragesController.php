<?php

namespace App\Http\Controllers;

use App\Http\Requests\CoverageRequest;
use App\Models\Coverage;
use Illuminate\Http\Request;

class CoveragesController extends Controller
{
    public function index()
    {
        $coverages = Coverage::all();

        return response()->json($coverages);
    }

    public function store(CoverageRequest $request)
    {
        $validated = $request->validated();

        $coverage = Coverage::create($validated);

        return response()->json($coverage, 201);
    }
}
