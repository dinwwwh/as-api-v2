<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use DB;
use Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Password;
use Storage;
use Str;

class AuthController extends Controller
{
    /**
     * Register and store user to database
     *
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();
            $sprite = $request->gender == 'other' ? 'human' : $request->gender;

            $user = User::create(array_merge(
                $request->only(['name', 'email', 'login']),
                [
                    'password' => Hash::make($request->password),
                    'avatar_path' => 'https://avatars.dicebear.com/api/' . $sprite . '/' . $request->name . '.svg',
                ]
            ));

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }

        auth()->login($user);
        return new UserResource(auth()->user());
    }

    /**
     * Handle login user
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
            'login' => ['required_without:email', 'string'],
            'email' => ['required_without:login', 'email'],
        ]);
        $credentials = $request->only('email', 'password', 'login');

        if (auth()->attempt($credentials, $request->remember)) {
            return new UserResource(auth()->user());
        }

        return response([
            'message' => 'Login failed.',
        ], 400);
    }

    /**
     * Read current authenticated user's infos
     *
     * @return \Illuminate\Http\Response
     */
    public function readProfile()
    {
        return new UserResource(auth()->user());
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        auth()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response([
            'message' => 'Logout success!!'
        ]);
    }

    /**
     * Handle update profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->only(['name', 'gender']);
            $oldAvatar = null;

            if ($request->hasFile('avatar')) {
                $data['avatar_path'] = $request->avatar->store('avatars', 'public');
                $oldAvatar = auth()->user()->avatar_path;
            }

            auth()->user()->update($data);

            Storage::delete($oldAvatar);
            DB::commit();
        } catch (\Throwable $th) {
            Storage::delete($data['avatar_path'] ?? null);
            DB::rollback();
            throw $th;
        }

        return response()->json(['message' => 'Updated profile successfully.']);
    }

    /**
     * Handle update password.
     *
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'oldPassword' => ['required', 'string'],
            'newPassword' => ['required', 'string', 'min:8'],
        ]);

        if (!Hash::check($request->oldPassword, auth()->user()->password)) {
            return response()->json([
                'message' => 'The old password is invalid.'
            ], 400);
        }

        auth()->user()->update([
            'password' => Hash::make($request->newPassword)
        ]);
        return response()->json(['message' => 'Updated password successfully.']);
    }

    /**
     * Send email resetting password.
     *
     * @return \Illuminate\Http\Response
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return response()->json([], 204);
    }

    /**
     * Handle resetting password when user forgot
     *
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return response()->json([], 204);
    }
}
