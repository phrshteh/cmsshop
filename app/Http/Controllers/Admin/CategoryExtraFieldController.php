<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\ExtraFieldCollection;
use App\Http\Resources\ExtraFieldResource;
use App\Http\Resources\NullResource;
use App\Models\Category;
use App\Models\ExtraField;
use Exception;
use Illuminate\Http\Request;

class CategoryExtraFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Category $category)
    {
        $data = $category->extraFields()->get();

        return (new ExtraFieldCollection($data))->response()->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Category $category)
    {
        $attributes = $request->validate(
            [
                'title'    => 'required|string',
                'key'      => 'required|string',
                'type'     => 'required|string|in:string,file',
                'optional' => 'required|boolean',
            ]
        );

        $extraField = $category->extraFields()->create($attributes);;

        return (new ExtraFieldResource($extraField))->response()->setStatusCode(201);

    }

    /**
     * Display the specified resource.
     *
     * @param  ExtraField  $extraField
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ExtraField $extraField)
    {
        return (new ExtraFieldResource($extraField))->response()->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  ExtraField  $extraField
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, ExtraField $extraField)
    {
        $attributes = $request->validate(
            [
                'title'    => 'sometimes|string',
                'key'      => 'sometimes|string',
                'type'     => 'sometimes|string|in:string,file',
                'optional' => 'sometimes|boolean',
            ]
        );

        $extraField->update($attributes);

        return (new ExtraFieldResource($extraField->refresh()))->response()->setStatusCode(202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  ExtraField  $extraField
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ExtraField $extraField)
    {
        try {
            $extraField->delete();

        } catch (Exception $exception) {

            return (new ExceptionResource($exception))->response()->setStatusCode(400);
        }

        return (new NullResource(null))->response()->setStatusCode(202);
    }
}
