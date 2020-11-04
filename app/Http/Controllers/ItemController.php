<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use App\Models\Category;
use App\Models\Item;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    use SoftDeletes;

    /**
     * Display a listing of the resource.
     *
     * @param ItemRequest $request
     * @return Item[]|Collection
     */
    public function index(ItemRequest $request)
    {
        $items = Item::with('categories');
        $filter = $request->query();

        if (!empty($filter['name'])) {
            $items->where(DB::raw('lower(items.name)'), 'like', '%' . $filter['name'] . '%');
        }
        if (!empty($filter['price'])) {
            $items->where('price', '>=', $filter['price'][0])
            ->where('price', '<=', $filter['price'][1]);
        }
        if (!empty($filter['published'])) {
            $items->where('is_published', '=', $filter['published']);
        }
        if(!empty($filter['id_category'])) {
            $items
                ->leftJoin('category_item', 'category_item.item_id', '=', 'items.id')
                ->leftJoin('categories', 'category_item.category_id', '=', 'categories.id')
                ->where('categories.id', $filter['id_category']);
        }
        if(!empty($filter['category_name'])) {
            $items
                ->leftJoin('category_item', 'category_item.item_id', '=', 'items.id')
                ->leftJoin('categories', 'category_item.category_id', '=', 'categories.id')
                ->where(DB::raw('lower(categories.name)'), 'like', '%' . $filter['category_name'] . '%');

        }

        return $items->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ItemRequest $request
     * @return void
     * @throws Exception
     */
    public function store(ItemRequest $request)
    {
        DB::beginTransaction();
        try {
            $item = (new Item())::create($request->validated());
            $categories = $request['categories'];
            $resultCategories = $this->prepareCategories($categories);
            if (count($resultCategories) >= 2 && count($resultCategories) <= 10) {
                $result = Category::find($resultCategories);
                $item->categories()->attach($result);
                DB::commit();
            } else {
                throw new Exception('2 to 10 categories allowed');
            }
        } catch (\Exception $e) {
            DB::rollBack();
        }

        return response()->json($item); //возвращает всегда инфу о товаре, даже если он не создан в итоге
    }

    /**
     * Display the specified resource.
     *
     * @param Item $item
     * @return Response
     */
    public function show($id)
    {
        return $item = Item::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ItemRequest $request
     * @param $id
     * @return Response
     */
    public function update(ItemRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $item = Item::find($id);
            $item->fill($request->except(['id']));
            $item->save();
            $item->categories()->detach();
            $newCategories = $request['categories'];
            $resultCategories = $this->prepareCategories($newCategories);
            if (count($resultCategories) >= 2 && count($resultCategories) <= 10) {
                $result = Category::find($resultCategories);
                $item->categories()->attach($result);
                DB::commit();
            } else {
                throw new Exception('2 to 10 categories allowed');
            }
        } catch (\Exception $e) {
            DB::rollBack();
        }
        return response()->json($item);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     * @return bool
     */
    public function destroy($id)
    {
        $item = Item::find($id);
        if($item->delete()) return response(true, 204);
    }

    public function prepareCategories($categories)
    {
        foreach ($categories as &$category) {
            $category = (int)trim($category);
        }
        $categories = array_unique($categories);
        $resultCategories = [];
        foreach ($categories as &$category) {
            $categoryToAdd = Category::find($category);
            if ($categoryToAdd !== null) {
                $resultCategories[] = $categoryToAdd->id; //отбираем только те категории, которые существуют
            }
        }
        return $resultCategories;
    }

}
