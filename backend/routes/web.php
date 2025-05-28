<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use \Laravel\Socialite\Facades\Socialite;

Route::get('/auth/redirect', function () {
    return Socialite::driver('keycloak')->stateless()->redirect();
});

Route::get('/auth/callback', function () {
    $externalUser = Socialite::driver('keycloak')->stateless()->user();

    $user = User::where('keycloak_id', $externalUser->id)->first();

    if (!$user) {
        User::create([
            'keycloak_id' => $externalUser->id,
        ]);
    }

    $nickname = $externalUser->getNickname();
    $token = $externalUser->token;

    $hiddenFields = [
        'token' => $externalUser->token,
        'nickname' => $nickname,
    ];
    return view('jump', compact('nickname', 'token', 'hiddenFields'));

});
