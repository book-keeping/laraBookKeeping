<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\AuthenticatedAction;
use App\Http\Responder\Settings\UpdateAccessTokenViewResponder;
use App\Service\AccessTokenService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UpdateAccessTokenActionHTML extends AuthenticatedAction
{
    /**
     * AccessTokenService responder instance.
     *
     * @var \App\Service\AccessTokenService
     */
    private $accessToken;

    /**
     * UpdateAccessTokenView responder instance.
     *
     * @var \App\Http\Responder\Settings\UpdateAccessTokenViewResponder
     */
    private $responder;

    /**
     * Create a new controller instance.
     *
     * @param \App\Http\Responder\Settings\UpdateAccessTokenViewResponder $responder
     *
     * @return void
     */
    public function __construct(AccessTokenService $accessToken, UpdateAccessTokenViewResponder $responder)
    {
        parent::__construct();
        $this->accessToken = $accessToken;
        $this->responder = $responder;
    }

    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request): Response
    {
        $context = [];
        $token = null;

        $this->accessToken->setUser($request->user());
        if ($request->isMethod('post')) {
            $token = $this->accessToken->generate();
        }
        if ($request->isMethod('delete')) {
            $this->accessToken->delete();
        }
        $timestamp = $this->accessToken->createdAt();

        $context['token'] = $token;
        $context['timestamp'] = $timestamp;

        return $this->responder->response($context);
    }
}
