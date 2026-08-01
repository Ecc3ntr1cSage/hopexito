<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }
    public function callbackGoogle(Request $request)
    {
        try {
            $user = Socialite::driver('google')->user();

            $findUser = User::where('google_id', $user->id)->first();

            if ($findUser) {
               Auth::login($findUser);
                return redirect()->intended(route('home'));
            } else {
                $user = User::create([
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'google_id' => $user->getId(),
                ]);
                Profile::create(['user_id' => $user->id]);
                Wallet::create([
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'name' => $user->name,
                    'commission' => 0,
                    'balance' => 0,
                    'status' => 1,
                    'user_id' => $user->id,
                ]);
                Auth::login($user);
                return redirect()->intended(route('home'));
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
