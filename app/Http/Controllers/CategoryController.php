<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Category[]|Collection
     */
    public function index()
    {
        return Category::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CategoryRequest $request
     * @return void
     */
    public function store(CategoryRequest $request)
    {
        $category = (new Category())::create($request->validated());
        return $category;

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return Response
     * @throws Exception
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        $items = $category->items()->get();
        if(!empty($items)) {
            throw new Exception("there's some items for this category, deleting cancelled");
        } else {
            if($category->delete()) return response(true, 204);
        }
    }
}
