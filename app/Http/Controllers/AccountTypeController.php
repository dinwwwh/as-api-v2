<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountType\CreateValidatorableRequest;
use App\Http\Requests\CreateAccountTypeRequest;
use App\Http\Requests\UpdateAccountTypeRequest;
use App\Http\Resources\AccountTypeResource;
use App\Models\AccountType;
use App\Models\Validator;
use App\Models\Validatorable;
use DB;

class AccountTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Get account types created by auth
     *
     * @return \Illuminate\Http\Response
     */
    public function getCreatedByMe()
    {
        $accountTypes = AccountType::where('creator_id', auth()->user()->getKey())
            ->orderBy('id', 'desc');

        if (request('_perPage')) {
            $accountTypes = $accountTypes->paginate(request('_perPage'));
        } else {
            $accountTypes = $accountTypes->get();
        }

        return AccountTypeResource::withLoad($accountTypes);
    }

    /**
     * Get usable account types
     *
     * @return \Illuminate\Http\Response
     */
    public function usable()
    {
        $usableAccountTypeIds = AccountType::whereRelation('users', 'id', auth()->user()->getKey())
            ->get(['id'])
            ->pluck('id')
            ->toArray();

        if ($search = request('_search')) {
            $accountTypes = AccountType::search($search);
        } else {
            $accountTypes = AccountType::orderBy('id', 'desc');
        }

        $accountTypes = $accountTypes->whereIn('id', $usableAccountTypeIds);

        if (request('_perPage')) {
            $accountTypes = $accountTypes->paginate(request('_perPage'));
        } else {
            $accountTypes = $accountTypes->get();
        }

        return AccountTypeResource::withLoad($accountTypes);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CreateAccountTypeRequest $request)
    {
        try {
            DB::beginTransaction();

            $accountType = AccountType::create($request->only('name', 'description'));
            $accountType->log('đã tạo kiểu tài khoản này.');
            $accountType->tag($request->tags);
            $accountType->user($request->users);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return AccountTypeResource::withLoad($accountType);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AccountType  $accountType
     * @return \Illuminate\Http\Response
     */
    public function show(AccountType $accountType)
    {
        return AccountTypeResource::withLoad($accountType);
    }

    /**
     * Create validatorable relationship
     *
     */
    public function createValidatorable(CreateValidatorableRequest $request, AccountType $accountType, Validator $validator)
    {
        try {
            DB::beginTransaction();

            $validatorable = $accountType->validatorables()->create([
                'validator_id' => $validator->getKey(),
                'type' => $request->type,
                'order' => $request->order,
                'mapped_readable_fields' => $request->mappedReadableFields,
                'mapped_updatable_fields' => $request->mappedUpdatableFields,
            ]);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return AccountTypeResource::withLoad($accountType);
    }

    /**
     * Delete validatorable relationship
     *
     */
    public function deleteValidatorable(AccountType $accountType, Validatorable $validatorable)
    {
        try {
            DB::beginTransaction();

            $validatorable->delete();

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return AccountTypeResource::withLoad($accountType);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAccountTypeRequest $request, AccountType $accountType)
    {
        try {
            DB::beginTransaction();

            $accountType->update($request->only('name', 'description'));
            $accountType->log('đã cập nhật thông tin');
            if ($request->tags) $accountType->tag($request->tags);
            if ($request->users) $accountType->user($request->users);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return AccountTypeResource::withLoad($accountType);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(AccountType $accountType)
    {
        //
    }
}
