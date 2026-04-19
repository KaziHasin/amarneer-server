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
            'properties' => Property::query()
                ->with(['category:id,name', 'user:id,name'])
                ->orderByDesc('created_at')
                ->get(),
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
    public function show(Property $property)
    {
        $property->load([
            'category:id,name',
            'user:id,name,email',
            'propertyGallery' => fn ($q) => $q->orderBy('id'),
        ]);

        $gallery = $property->propertyGallery->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => $item->type,
                'url' => self::publicStorageUrl($item->file_path),
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

    /**
     * Build a browser URL for a public disk path. Uses a root-relative URL so the
     * current host (e.g. Valet .test) is used even when APP_URL in .env differs.
     */
    private static function publicStorageUrl(?string $filePath): ?string
    {
        if ($filePath === null) {
            return null;
        }

        $raw = trim(str_replace('\\', '/', (string) $filePath));
        if ($raw === '') {
            return null;
        }

        if (preg_match('#^https?://#i', $raw)) {
            return $raw;
        }

        $path = ltrim($raw, '/');
        if (str_starts_with($path, 'storage/')) {
            return '/'.$path;
        }

        return '/storage/'.$path;
    }
}
