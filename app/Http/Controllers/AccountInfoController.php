<?php

namespace App\Http\Controllers;

use App\Actions\CreateAccountInfo;
use App\Http\Resources\AccountInfoResource;
use App\Models\AccountInfo;
use App\Models\AccountType;
use DB;
use Illuminate\Http\Request;

class AccountInfoController extends Controller
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
    public function create(Request $request, AccountType $accountType)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $data['account_type_id'] = $accountType->getKey();
            $accountInfo = AccountInfo::create($data);
            if ($request->rules) $accountInfo->rule($request->rules);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return AccountInfoResource::withLoad($accountInfo);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(AccountInfo $accountInfo)
    {
        return AccountInfoResource::withLoad($accountInfo);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AccountInfo $accountInfo)
    {
        try {
            DB::beginTransaction();

            $accountInfo->update($request->all());
            if (is_array($request->rules)) $accountInfo->rule($request->rules);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return AccountInfoResource::withLoad($accountInfo);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(AccountInfo $accountInfo)
    {
        //
    }
}
