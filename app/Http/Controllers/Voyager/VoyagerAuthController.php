<?php

namespace App\Http\Controllers\Voyager;

use Illuminate\Support\Facades\Auth;
use TCG\Voyager\Http\Controllers\VoyagerAuthController as BaseVoyagerAuthController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use TCG\Voyager\Facades\Voyager;

class VoyagerAuthController extends BaseVoyagerAuthController
{
    use AuthenticatesUsers;

    public function login()
    {
        if (Auth::user()) {
            return redirect()->route('voyager.pedidos.index');
        }

        return Voyager::view('voyager::login');
    }
    /*
    * Preempts $redirectTo member variable (from RedirectsUsers trait)
    */
    public function redirectTo()
    {
        return config('voyager.user.redirect', route('voyager.pedidos.index'));
    }
}
