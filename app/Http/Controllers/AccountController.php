<?php

namespace App\Http\Controllers;

use App\Http\Requests\Account\ConfirmRequest;
use App\Http\Requests\Account\CreateRequest;
use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Models\AccountType;
use DB;
use Illuminate\Http\Request;
use Storage;

class AccountController extends Controller
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
     * Get accounts that created by me
     *
     */
    public function getCreatedByMe()
    {
        if (request('_search')) {
            $accounts = AccountType::search(request('_search'))
                ->where('creator_id', auth()->user()->getKey());
        } else {
            $accounts = AccountType::where('creator_id', auth()->user()->getKey())
                ->orderBy('id', 'desc');
        }

        if (request('_perPage')) {
            $accounts = $accounts->paginate(request('_perPage'));
        } else {
            $accounts = $accounts->get();
        }

        return AccountResource::withLoad($accounts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(CreateRequest $request, AccountType $accountType, Account $account)
    {
        try {
            DB::beginTransaction();

            $data = $request->only('description', 'cost', 'price');
            $data['account_type_id'] = $accountType->getKey();
            $data['tax'] = 0;

            $account = $account->create($data);
            $account->tag($request->tags);

            foreach ($request->images as $order => $image) {
                $account->images()->create([
                    'order' => $order,
                    'path' => $image->store('account-images', 'public'),
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            Storage::delete($account->images->pluck('path'));
            DB::rollBack();
            throw $th;
        }

        return AccountResource::withLoad($account);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        return AccountResource::withLoad($account);
    }

    /**
     * Handle buy an account for user.
     *
     * @return \Illuminate\Http\Response
     */
    public function buy(Account $account)
    {
        try {
            DB::beginTransaction();

            $bestPrice = $account->price;
            auth()->user()->updateBalance(-$bestPrice, 'mua tài khoản #' . $account->getKey());
            $account->update([
                'bought_at' => now(),
                'buyer_id' => auth()->user()->getKey(),
                'confirmed_at' => now()->addHour(), // Auto confirmed after 1 hour
                'bought_at_price' => $bestPrice,
            ]);
            $account->log('mua tài khoản');

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return AccountResource::withLoad($account);
    }

    /**
     * buyer confirm that bought account is oke or not.
     *
     * @return \Illuminate\Http\Response
     */
    public function confirm(ConfirmRequest $request, Account $account)
    {
        try {
            DB::beginTransaction();

            if ($account->confirmed_at && now()->lte($account->confirmed_at)) {
                $account->update([
                    'confirmed_at' => $request->oke ? now() : null,
                ]);

                if ($request->oke) $account->log('xác nhận là tài khoản đúng thông tin');
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return AccountResource::withLoad($account);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account)
    {
        try {
            DB::beginTransaction();
            $newImagePaths = [];

            $data = $request->only('description', 'cost', 'price');

            $account->update($data);
            if ($request->tags) $account->tag($request->tags);

            if ($request->images) {
                $oldImages = $account->images;
                foreach ($request->images as $order => $image) {
                    $account->images()->create([
                        'order' => $order,
                        'path' => $newImagePaths[] = $image->store('account-images', 'public'),
                    ]);
                }
                $oldImages->each(fn ($oldImage) => $oldImage->delete());
            }

            DB::commit();
        } catch (\Throwable $th) {
            Storage::delete($newImagePaths);
            DB::rollBack();
            throw $th;
        }

        return AccountResource::withLoad($account);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Account $account)
    {
        //
    }
}
