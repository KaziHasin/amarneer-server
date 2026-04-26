<?php

namespace Modules\Properties\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Modules\Properties\Http\Requests\StoreProperty;
use Modules\Properties\Models\Property;
use Modules\Properties\Transformers\PropertyResource;
use Modules\Plans\Services\UserPlanEntitlementService;

class PropertiesController extends Controller
{
    /** 
     * Get all properties
     * GET /api/properties
     * @return  AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Property::with(['category', 'propertyGallery'])
            ->where('status', 'approved');

        // Featured
        if ($request->featured) {
            $query->where('is_featured', true);
        }

        // Search
        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('location', 'LIKE', "%{$search}%")
                    ->orWhere('area', 'LIKE', "%{$search}%");
            });
        }

        // Category
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Price range
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        $properties = $query->latest()->paginate(10);

        return PropertyResource::collection($properties);
    }

    /** 
     * Store a property
     * POST /api/properties
     * @return  PropertyResource
     */
    public function store(StoreProperty $request): PropertyResource|JsonResponse
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $data['status'] = 'pending';

            $property = Property::create($data);

            if ($request->has('amenities')) {
                $property->amenities()->sync($request->amenities);
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {

                    $path = $image->store('properties', 'public');

                    $property->propertyGallery()->create([
                        'file_path' => $path,
                    ]);
                }
            }

            DB::commit();

            return new PropertyResource($property->load('propertyGallery'));

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /** 
     * Get a property
     * GET /api/properties/{id}
     * @return  PropertyResource
     */

    public function show($id): PropertyResource
    {
        $property = Property::with(['category', 'propertyGallery'])->findOrFail($id);

        return new PropertyResource($property);
    }

    /** 
     * Get a property
     * PUT /api/properties/{id}
     * @return  PropertyResource
     */
    public function update(Request $request, $id): PropertyResource
    {
        $property = Property::findOrFail($id);

        $data = $request->validate([
            'title' => 'sometimes|string',
            'category_id' => 'sometimes|exists:categories,id',
            'listing_type' => 'sometimes|in:rent,sale',
            'price' => 'sometimes|numeric',
            'area' => 'sometimes|numeric',
            'city' => 'sometimes|string',
            'location' => 'sometimes|string',
            'description' => 'nullable|string',
        ]);

        $property->update($data);

        return new PropertyResource($property);
    }
    /** 
     * Get a property
     * DELETE /api/properties/{id}
     * @return  JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $property = Property::findOrFail($id);
        $property->delete();

        return response()->json([
            'message' => 'Property deleted successfully'
        ]);
    }

    /**
     * Unlock property owner contact details (consumes 1 contact unlock from active plan).
     * POST /api/v1/properties/{property}/unlock-contact
     */
    public function unlockContact(Request $request, Property $property, UserPlanEntitlementService $entitlements): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (!$property->user_id) {
            return response()->json(['message' => 'Owner contact not available for this property.'], 422);
        }

        $property->load('user:id,name,email');

        $consumed = $entitlements->consumeContactUnlock($user);
        if (!$consumed) {
            return response()->json([
                'message' => 'No active plan or contact limit reached. Please buy a plan.',
            ], 402);
        }

        return response()->json([
            'data' => [
                'owner' => [
                    'id' => $property->user?->id,
                    'name' => $property->user?->name,
                    'email' => $property->user?->email,
                ],
            ],
        ]);
    }
}
