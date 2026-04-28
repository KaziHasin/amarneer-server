<?php

namespace Modules\Properties\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Properties\Models\Amenity;

class AmenitiesController extends Controller
{
    public function index(Request $request)
    {
        $query = Amenity::query();

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $amenities = $query->orderBy('name')->get();

        return response()->json([
            'data' => $amenities
        ]);
    }
}
