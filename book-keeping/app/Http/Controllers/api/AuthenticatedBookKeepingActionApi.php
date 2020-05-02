<?php

namespace App\Http\Controllers\api;

use App\Service\BookKeepingService;

class AuthenticatedBookKeepingActionApi extends AuthenticatedActionApi
{
    /**
     * BookKeeping service instance.
     *
     * @var \App\Service\BookKeepingService
     */
    protected $BookKeeping;

    /**
     * Create a new AuthenticatedBookKeepingActionApi instance.
     *
     * @param \App\Service\BookKeepingService $BookKeeping
     */
    public function __construct(BookKeepingService $BookKeeping)
    {
        parent::__construct();
        $this->BookKeeping = $BookKeeping;
    }
}
