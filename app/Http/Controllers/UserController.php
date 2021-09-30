<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
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
    public function show($id)
    {
        //
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
