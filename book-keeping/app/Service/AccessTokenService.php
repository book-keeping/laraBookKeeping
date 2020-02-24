<?php

namespace App\Service;

use App\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AccessTokenService
{
    /**
     * a user who has the access token.
     *
     * @var \App\User
     */
    private $user;

    /**
     * Get the time the token was created.
     *
     * @return string | null
     */
    public function createdAt()
    {
        $timestamp = null;

        if (isset($this->user)) {
            $timestamp = $this->user->api_token_created_at;
        }

        return $timestamp;
    }

    /**
     * Delete the token.
     *
     * @return void
     */
    public function delete()
    {
        if (isset($this->user)) {
            $this->user->forceFill([
                'api_token'            => null,
                'api_token_created_at' => null,
            ])->save();
        }
    }

    /**
     * Generate new token.
     *
     * @return string | null
     */
    public function generate()
    {
        $token = null;

        if (isset($this->user)) {
            $token = Str::random(60);
            $this->user->forceFill([
                'api_token'            => hash('sha256', $token),
                'api_token_created_at' => new Carbon(),
            ])->save();
        }

        return $token;
    }

    /**
     * Create a new AccessTokenService instance.
     *
     * @param \App\User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }
}
