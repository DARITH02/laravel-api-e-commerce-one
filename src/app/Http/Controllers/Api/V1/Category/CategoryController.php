<?php

namespace App\Http\Controllers\Api\V1\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category\CategoryResource;
use App\Services\Category\CategoryService;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $service) {}
    public function index()
    {
    $categories = $this->service->list();

        return response()->json([
            'success'=>true,
            'categories'=>"Category list fetched successfully",
            'data'=>CategoryResource::collection($categories),
            'pagination'=>[
                'current_page'=>$categories->currentPage(),
                'per_page'=>$categories->perPage(),
                'total'=>$categories->total(),
                'last_page'=>$categories->lastPage(),
            ]
        ]);
        // return response()->json(['data'=>10]);
    }
}
