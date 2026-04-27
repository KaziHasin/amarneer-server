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
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Category::query()->select('categories.*')
            ->selectRaw('(SELECT COUNT(*) FROM properties WHERE properties.status = ? AND (properties.category_id = categories.id OR properties.category_id IN (SELECT id FROM categories AS c2 WHERE c2.parent_id = categories.id))) as properties_count', ['approved']);

        if ($request->filled('type') && $request->input('type') === 'child') {
            $query->whereNotNull('parent_id');
        } elseif ($request->filled('parent_id')) {
            $query->where('parent_id', $request->input('parent_id'));
        } else {
            $query->whereNull('parent_id');
        }

        if ($request->search) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $categories = $query->orderBy('name')->get();

        return CategoryResource::collection($categories);
    }


}
