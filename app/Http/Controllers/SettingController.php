<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ($search = request('_search')) {
            $settings = Setting::search($search);
        } else {
            $settings = Setting::orderBy('created_at', 'desc');
        }

        if ($perPage = request('_perPage')) {
            $settings = $settings->paginate($perPage);
        } else {
            $settings = $settings->get();
        }

        return SettingResource::withLoad($settings);
    }

    /**
     * Get public settings
     *
     * @return \Illuminate\Http\Response
     */
    public function getPublicSettings(Request $request)
    {
        if ($request->_perPage) {
            $settings = Setting::where('public', true)
                ->paginate($request->_perPage);
        } else {
            $settings = Setting::where('public', true)
                ->get();
        }

        return SettingResource::withLoad($settings);
    }

    /**
     * Create a new setting.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        return SettingResource::withLoad($setting);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSettingRequest $request, Setting $setting)
    {
        try {
            DB::beginTransaction();

            $setting->update($request->only(['description', 'value']));

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return SettingResource::withLoad($setting);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function delete(Setting $setting)
    {
        //
    }
}
