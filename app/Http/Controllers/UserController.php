<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateBalanceRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use DB;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get users
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ($search = request('_search')) {
            $users = User::search($search);
        } else {
            $users = User::orderBy('id', 'desc');
        }

        if ($perPage = request('_perPage')) {
            $users = $users->paginate($perPage);
        } else {
            $users = $users->get();
        }

        return UserResource::withLoad($users);
    }

    /**
     * Find user by compare strictly.
     *
     * @return \Illuminate\Http\Response
     */
    public function findStrictly(Request $request)
    {
        $user = User::where('id', $request->_search)
            ->orWhere('email', $request->_search)
            ->orWhere('login', $request->_search)
            ->first();

        return UserResource::withLoad($user);
    }

    /**
     * Find users by compare like strictly.
     *
     * @return \Illuminate\Http\Response
     */
    public function searchStrictly(Request $request)
    {
        $users = User::where('id', $request->_search)
            ->orWhere('email', 'like', '%' . $request->_search . '%')
            ->orWhere('login', 'like', '%' . $request->_search . '%')
            ->orWhere('name', 'like', '%' . $request->_search . '%');

        if ($request->_perPage) {
            $users = $users->paginate($request->_perPage);
        } else {
            $users = $users->get();
        }

        return UserResource::withLoad($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return UserResource::withLoad($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Handle update balance of user
     *
     * @return \Illuminate\Http\Response
     */
    public function updateBalance(UpdateBalanceRequest $request, User $user)
    {
        try {
            DB::beginTransaction();

            $user->updateBalance($request->amount, $request->description);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }

        return UserResource::withLoad($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
