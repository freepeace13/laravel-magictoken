<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Str;
use MagicToken\Http\Controllers\AccessTokensForm;
use MagicToken\Http\Controllers\VerifyTokensPincode;
use MagicToken\Http\Controllers\RegenerateFreshToken;
use MagicToken\Middlewares\ValidateToken;
use MagicToken\Middlewares\ValidateSignature;

Route::group([
    'prefix' => '{token}',
    'middleware' => [ValidateSignature::class, ValidateToken::class],
], function () {
    Route::get('/verify', AccessTokensForm::class)->name('magictoken.verify');
    Route::post('/verify', VerifyTokensPincode::class)->name('magictoken.verify');
    Route::post('/resend', RegenerateFreshToken::class)->name('magictoken.resend');
});
