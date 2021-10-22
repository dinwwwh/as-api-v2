<?php

namespace App\Http\Controllers;

use App\Http\Requests\Validator\CreateRequest;
use App\Http\Requests\Validator\UpdateRequest;
use App\Http\Resources\ValidatorResource;
use App\Models\Validator;
use Arr;
use DB;
use Illuminate\Http\Request;

class ValidatorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        if ($search = request('_search')) {
            $validators = Validator::search($search);
        } else {
            $validators = Validator::orderBy('updated_at', 'desc');
        }

        if ($creatorId = request('_creatorId')) {
            $validators = $validators->where('creator_id', $creatorId);
        }

        if ($perPage = request('_perPage')) {
            $validators =  $validators->paginate($perPage);
        } else {
            $validators = $validators->get();
        }

        return ValidatorResource::withLoad($validators);
    }

    /**
     * Store a newly created resource in storage.
     *
     */
    public function create(CreateRequest $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::create(
                Arr::snake($request->only([
                    'name',
                    'description',
                    'approverDescription',
                    'readableFields',
                    'updatableFields',
                    'fee',
                ]))
            );

            $validator->user($request->users);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return ValidatorResource::withLoad($validator->refresh());
    }

    /**
     * Display the specified resource.
     *
     */
    public function show(Validator $validator)
    {
        return ValidatorResource::withLoad($validator);
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(UpdateRequest $request, Validator $validator)
    {
        try {
            DB::beginTransaction();

            $validator->update(
                Arr::snake($request->only([
                    'name',
                    'description',
                    'approverDescription',
                    'readableFields',
                    'updatableFields',
                    'fee',
                ]))
            );

            if ($request->has('users'))
                $validator->user($request->users);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return ValidatorResource::withLoad($validator);
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function delete(Validator $validator)
    {
        //
    }
}
