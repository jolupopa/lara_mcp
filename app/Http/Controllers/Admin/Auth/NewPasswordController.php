<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Laravel\Fortify\Contracts\NewPasswordResponse;
use Laravel\Fortify\Contracts\ResetsUserPasswords;
use Inertia\Inertia;
use Laravel\Fortify\Features;

class NewPasswordController extends Controller
{
    /**
     * The resets user passwords implementation.
     *
     * @var \Laravel\Fortify\Contracts\ResetsUserPasswords
     */
    protected $resetsUserPasswords;

    /**
     * Create a new controller instance.
     *
     * @param  \Laravel\Fortify\Contracts\ResetsUserPasswords  $resetsUserPasswords
     * @return void
     */
    public function __construct(ResetsUserPasswords $resetsUserPasswords)
    {
        $this->resetsUserPasswords = $resetsUserPasswords;
    }

    /**
     * Show the new password view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function create(Request $request): \Inertia\Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->route('token'),
            'guard' => 'admin', // Indicate the guard for the frontend
        ]);
    }

    /**
     * Reset the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Fortify\Contracts\NewPasswordResponse
     */
    public function store(Request $request): NewPasswordResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on the records and then redirect to the home
        // route. If an error occurs we will send the user back to where they
        // started from with the error message.
        $status = $this->resetsUserPasswords->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                Auth::guard('admin')->login($user); // Log in the admin user
            },
            'admins' // Use the 'admins' password broker
        );

        return $status == Password::PASSWORD_RESET
                    ? app(NewPasswordResponse::class, ['status' => $status])
                    : back()->withErrors(['email' => trans($status)]);
    }
}
