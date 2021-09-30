<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountTypeRequest;
use App\Http\Requests\UpdateAccountTypeRequest;
use App\Http\Resources\AccountTypeResource;
use App\Models\AccountType;
use DB;
use Illuminate\Http\Request;

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

        return new AccountTypeResource($accountType->load($request->_relationships ?? []));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AccountType  $accountType
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, AccountType $accountType)
    {
        return new AccountTypeResource($accountType->load($request->_relationships ?? []));
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

        return new AccountTypeResource($accountType->load($request->_relationships ?? []));
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
