<?php

namespace Modules\Properties\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Properties\Models\Property;
use Modules\Properties\Services\ProperiesSerialsService;

class PropertiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Properties/Index', [
            'properties' => Property::query()
                ->with(['category:id,name', 'user:id,name'])
                ->orderByDesc('created_at')
                ->get(),
        ]);
    }


    /**
     * Show the specified resource.
     */
    public function show(Property $property)
    {
        $property->load([
            'category:id,name',
            'user:id,name',
            'propertyGallery' => fn($q) => $q->orderBy('id'),
        ]);

        $gallery = $property->propertyGallery->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => $item->type,
                'url' => (new ProperiesSerialsService())->getPublicPath($item->file_path),
            ];
        })->values()->all();

        return Inertia::render('Properties/Show', [
            'property' => [
                'id' => $property->id,
                'name' => $property->name,
                'slug' => $property->slug,
                'description' => $property->description,
                'price' => $property->price,
                'area' => $property->area,
                'location' => $property->location,
                'listing_type' => $property->listing_type,
                'status' => $property->status,
                'is_featured' => (bool) $property->is_featured,
                'created_at' => $property->created_at?->toIso8601String(),
                'category' => $property->category,
                'user' => $property->user,
                'gallery' => $gallery,
            ],
        ]);
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


}
