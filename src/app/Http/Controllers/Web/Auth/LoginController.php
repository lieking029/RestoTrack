<?php

namespace App\Http\Controllers\Web\Auth;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\Auth\Login\ValidateUserCredentialRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(ValidateUserCredentialRequest $request)
    {
        $user = User::where('email', $request->email)
            ->whereIn('user_type', [UserType::Admin, UserType::Manager])
            ->first();

        if(!$user) {
            return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        if(!\Hash::check($request->password, $user->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['password' => 'These credentials do not match our records.']);
        }

        Auth::login($user);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
