<?php

namespace Modules\Properties\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Properties\Models\Property;

class PropertiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Properties/Index', [
            'properties' => Property::orderBy('created_at', 'desc')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('properties::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('properties::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('properties::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|string',
            'slug' => 'sometimes|nullable|string',
            'category_id' => 'sometimes|exists:categories,id',
            'listing_type' => 'sometimes|in:rent,sale',
            'price' => 'sometimes|numeric',
            'area' => 'sometimes|numeric',
            'location' => 'sometimes|string',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|in:pending,approved,rejected',
            'is_featured' => 'sometimes|boolean',
        ]);

        $property->update($data);

        return redirect()->back()->with('success', 'Property updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
    }
}
