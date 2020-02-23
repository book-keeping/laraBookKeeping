<?php

namespace App\Http\Controllers;

class AuthenticatedAction extends Controller
{
    /**
     * Create a new AuthenticatedAction instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }
}
