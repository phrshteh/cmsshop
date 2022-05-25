<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\NullResource;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = Category::query()->with('parent')->latest();

        if (\request()->input('dropdown')) {
            $data = $categories->get(['id', 'title']);
        } else {
            $data = $categories->paginate(\request()->input('per_page') ?? 15);
        }

        return (new CategoryCollection($data))->response()->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CategoryRequest  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CategoryRequest $request)
    {
        $categoryAttributes   = $request->safe(['title', 'slug', 'parent_id', 'thumbnail_height', 'thumbnail_width']);
        $extraFieldAttributes = $request->safe(['fields']);


        DB::beginTransaction();
        try {

            $category = Category::create($categoryAttributes);

            if ( ! empty($extraFieldAttributes['fields'])) {
                foreach ($extraFieldAttributes['fields'] as $attribute) {
                    $category->extraFields()->create(
                        [
                            'title'    => $attribute['title'],
                            'key'      => $attribute['key'],
                            'type'     => $attribute['type'],
                            'optional' => $attribute['optional'],
                        ]
                    );
                }
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return (new ExceptionResource($exception))->response()->setStatusCode(400);
        }

        return (new CategoryResource($category))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Category $category)
    {
        $category = $category->load(['children', 'extraFields']);

        return (new CategoryResource($category))->response()->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CategoryRequest  $request
     * @param  \App\Models\Category  $category
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $categoryAttributes = $request->safe(['title', 'slug', 'parent_id', 'thumbnail_height', 'thumbnail_width']);

        $category->update($categoryAttributes);

        return (new CategoryResource($category->refresh()))->response()->setStatusCode(202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Category $category)
    {
        if ($category->contents()->exists()) {

            return (new NullResource($category))->response()->setStatusCode(423)->setData(
                [
                    'message' => trans('admin.categories.destroy.failed.contents'),
                ]
            );
        }
        try {
            $category->delete();

        } catch (Exception $exception) {

            return (new ExceptionResource($exception))->response()->setStatusCode(400);
        }

        return (new NullResource(null))->response()->setStatusCode(202);
    }
}
