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
use Modules\Properties\Services\ProperiesSerialsService;
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

        if ($request->featured) {
            $query->where('is_featured', true);
        }

        if ($request->search) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $searchTerm = '%' . strtolower($search) . '%';
                $q->whereRaw('LOWER(name) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(location) LIKE ?', [$searchTerm])
                    ->orWhereRaw('LOWER(area) LIKE ?', [$searchTerm]);
            });
        }

        if ($request->category_id) {
            $query->where(function ($q) use ($request) {
                $q->where('category_id', $request->category_id)
                    ->orWhereIn('category_id', function ($sub) use ($request) {
                        $sub->select('id')->from('categories')->where('parent_id', $request->category_id);
                    });
            });
        }

        if ($request->listing_type) {
            $query->where('listing_type', $request->listing_type);
        }

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
     * Get max price of properties
     * GET /api/properties/max-price
     * @return  JsonResponse
     */
    public function getMaxPrice(): JsonResponse
    {
        $maxPrice = Property::max('price') ?? 0;
        return response()->json(['data' => ['max_price' => $maxPrice]]);
    }

    /** 
     * Get properties stats
     * GET /api/properties/stats
     * @return  JsonResponse
     */
    public function getStats(): JsonResponse
    {
        $totalProperties = Property::where('status', 'approved')->count();
        $totalAreas = Property::where('status', 'approved')->distinct('location')->count('location');
        return response()->json([
            'data' => [
                'total_properties' => $totalProperties,
                'total_areas' => $totalAreas,
            ]
        ]);
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

            $ownerData = [
                'name' => $data['owner_name'],
                'email' => $data['owner_email'] ?? null,
                'mobile' => $data['owner_phone'],
            ];

            $owner = (new ProperiesSerialsService())->createOrUpdatePropertyOwner($ownerData);

            $propertyData = collect($data)->except(['owner_name', 'owner_phone', 'owner_email'])->toArray();
            $propertyData['user_id'] = $owner->id;

            $property = Property::create($propertyData);

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

    public function show($identifier): PropertyResource
    {
        $property = Property::with(['category', 'propertyGallery', 'amenities'])
            ->where('slug', $identifier)
            ->orWhere('id', $identifier)
            ->firstOrFail();

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
     * Idempotent: if the user already unlocked this property under the current plan,
     * the contact is returned without consuming an additional credit.
     * POST /api/v1/properties/{property}/unlock-contact
     */
    public function unlockContact(Request $request, Property $property, UserPlanEntitlementService $entitlements): JsonResponse
    {
        $user = $request->user();

        if (!$property->user_id) {
            return response()->json(['message' => 'Owner contact not available for this property.'], 422);
        }

        $result = $entitlements->consumeContactUnlock($user, $property->id);

        if (!$result['consumed']) {
            return response()->json([
                'message' => 'No active plan or contact limit reached. Please subscribe to a plan.',
                'code' => 'plan_required',
            ], 402);
        }

        $property->load('user:id,name,email,mobile');

        $activePlan = $entitlements->getActiveUserPlan($user);

        return response()->json([
            'data' => [
                'owner' => [
                    'name' => $property->user?->name,
                    'email' => $property->user?->email,
                    'mobile' => $property->user?->mobile,
                ],
                'contacts_remaining' => $activePlan?->plan?->contact_limit === null
                    ? null
                    : max(0, ($activePlan->plan->contact_limit - $activePlan->contacts_used)),
                'already_unlocked' => $result['already_unlocked'],
            ],
        ]);
    }

    /**
     * Check if the authenticated user has already unlocked this property's contact
     * under their current active plan. Returns the owner contact if already unlocked.
     * GET /api/v1/properties/{property}/unlock-status
     */
    public function unlockStatus(Request $request, Property $property, UserPlanEntitlementService $entitlements): JsonResponse
    {
        $user = $request->user();

        $alreadyUnlocked = $entitlements->hasAlreadyUnlockedProperty($user, $property->id);

        if (!$alreadyUnlocked) {
            return response()->json(['data' => ['unlocked' => false]]);
        }

        $property->load('user:id,name,email,mobile');
        $activePlan = $entitlements->getActiveUserPlan($user);

        return response()->json([
            'data' => [
                'unlocked' => true,
                'owner' => [
                    'name' => $property->user?->name,
                    'email' => $property->user?->email,
                    'mobile' => $property->user?->mobile,
                ],
                'contacts_remaining' => $activePlan?->plan?->contact_limit === null
                    ? null
                    : max(0, ($activePlan->plan->contact_limit - $activePlan->contacts_used)),
            ],
        ]);
    }
}
