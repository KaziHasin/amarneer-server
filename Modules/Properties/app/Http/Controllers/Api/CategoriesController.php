<?php

namespace Modules\Properties\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Properties\Models\Category;
use Modules\Properties\Transformers\CategoryResource;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Category::query();

        if ($request->search) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $categories = $query->orderBy('name')->paginate(50);

        return CategoryResource::collection($categories);
    }


}
