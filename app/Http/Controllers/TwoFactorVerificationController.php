<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwoFactorVerificationController extends Controller
{
    // verify 2FA
    public function verify(){
        return view('2fa.verify');

    }

}
