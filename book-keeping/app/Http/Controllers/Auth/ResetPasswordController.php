<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param \Illuminate\Http\Request $request
     * @param string|null              $token
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showResetForm(Request $request, $token = null)
    {
        $user = $this->broker()->getUser($credentials = $request->only('email', 'name'));

        if (is_null($user) || !$this->broker()->getRepository()->exists($user, $token)) {
            return redirect(route('password.request'))->with('invalid-link', trans('passwords.link'));
        } else {
            return view('auth.passwords.reset')->with(
                ['token' => $token, 'name' => $user->getIndexForPasswordReset('name')]
            );
        }
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token'    => 'required',
            'name'     => 'required',
            'password' => 'required|confirmed|min:8',
        ];
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'name', 'password', 'password_confirmation', 'token'
        );
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $response
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return redirect(route('password.request'))->with('invalid-link', trans('passwords.link'));
    }
}
