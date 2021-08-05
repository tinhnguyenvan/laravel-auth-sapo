<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SapoAuthController extends Controller
{
    public function index()
    {
        if (!empty(auth()->id())) {
            return redirect('dashboard');
        }

        return view('login.index');
    }

    public function redirectToProvider(Request $request)
    {
        if (!empty(auth()->id())) {
            return redirect('dashboard');
        }

        $params = [
            'store' => $request->get('store'),
        ];
        return Socialite::driver('sapo')->with($params)->redirect();
    }

    public function handleProviderCallback(Request $request)
    {
        $params = [
            'store' => Str::before($request->get('store'), '.'),
        ];

        $user = Socialite::driver('sapo')->with($params)->user();

        $dataUser = [
            'email' => $user->getEmail(),
        ];

        $dataValue = [
            'name' => $user->getName(),
            'password' => $user->token,
            'remember_token' => $user->token,
        ];

        $myUser = User::query()->updateOrCreate($dataUser, $dataValue);

        if (Auth::login($myUser)) {
            return route('dashboard');
        }

        return redirect('/');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

}
