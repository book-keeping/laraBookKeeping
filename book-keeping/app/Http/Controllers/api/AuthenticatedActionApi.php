<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;

class AuthenticatedActionApi extends Controller
{
    /**
     * Create a new AuthenticatedActionApi instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
}
