<?php

namespace App\Http\Controllers\Admin\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Password;
use Laravel\Fortify\Contracts\PasswordResetLinkRequestResponse;
use Laravel\Fortify\Contracts\SendsPasswordResetLink;
use Inertia\Inertia;
use Laravel\Fortify\Features;

class PasswordResetLinkController extends Controller
{
    /**
     * The sends password reset link implementation.
     *
     * @var \Laravel\Fortify\Contracts\SendsPasswordResetLink
     */
    protected $sendsPasswordResetLink;

    /**
     * Create a new controller instance.
     *
     * @param  \Laravel\Fortify\Contracts\SendsPasswordResetLink  $sendsPasswordResetLink
     * @return void
     */
    public function __construct(SendsPasswordResetLink $sendsPasswordResetLink)
    {
        $this->sendsPasswordResetLink = $sendsPasswordResetLink;
    }

    /**
     * Show the form for requesting a password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function create(Request $request): \Inertia\Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => $request->session()->get('status'),
            'guard' => 'admin', // Indicate the guard for the frontend
        ]);
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Fortify\Contracts\PasswordResetLinkRequestResponse
     */
    public function store(Request $request): PasswordResetLinkRequestResponse
    {
        $request->validate(['email' => 'required|email']);

        // We will send the password reset link to this user. Once we have them, we will
        // send a message back to their browser to indicate that they have received
        // the link. Once you have this working, you may customize the message.
        $status = $this->sendsPasswordResetLink->send(
            $request->only('email'),
            'admins' // Use the 'admins' password broker
        );

        return $status == Password::RESET_LINK_SENT
                    ? app(PasswordResetLinkRequestResponse::class, ['status' => $status])
                    : back()->withErrors(['email' => trans($status)]);
    }
}
