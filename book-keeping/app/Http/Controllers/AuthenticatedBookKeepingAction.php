<?php

namespace App\Http\Controllers;

use App\Service\BookKeepingService;

class AuthenticatedBookKeepingAction extends Controller
{
    /**
     * BookKeeping service instance.
     *
     * @var \App\Service\BookKeepingService
     */
    protected $BookKeeping;

    /**
     * Create a new AuthenticatedBookKeepingAction instance.
     *
     * @param \App\Service\BookKeepingService $BookKeeping
     */
    public function __construct(BookKeepingService $BookKeeping)
    {
        $this->middleware('auth');
        $this->middleware('verified');
        $this->BookKeeping = $BookKeeping;
    }
}
