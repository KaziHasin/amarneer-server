<?php

namespace Modules\Properties\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Properties\Models\Amenity;

class AmenitiesController extends Controller
{
    public function index()
    {
        $amenities = Amenity::orderBy('name')->get();

        return response()->json([
            'data' => $amenities
        ]);
    }
}
