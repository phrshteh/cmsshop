<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\NullResource;
use App\Http\Resources\SettingCollection;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = Setting::latest()->paginate(\request()->input('per_page') ?? 15);

        return (new SettingCollection($data))->response()->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $attributes = $request->validate(
            [
                'title' => 'nullable|string',
                'key'   => 'required|string',
                'value' => 'required|string',
            ]
        );

        $setting = Setting::create($attributes);

        return (new SettingResource($setting))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Setting  $setting
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Setting $setting)
    {
        return (new SettingResource($setting))->response()->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Setting  $setting
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Setting $setting)
    {
        $attributes = $request->validate(
            [
                'title' => 'nullable|string',
                'key'   => 'sometimes|string',
                'value' => 'sometimes|string',
            ]
        );

        $setting->update($attributes);

        return (new SettingResource($setting->refresh()))->response()->setStatusCode(202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Setting  $setting
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Setting $setting)
    {
        DB::beginTransaction();
        try {
            $setting->delete();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();

            return (new ExceptionResource($exception))->response()->setStatusCode(400);
        }

        return (new NullResource(null))->response()->setStatusCode(202);
    }
}
