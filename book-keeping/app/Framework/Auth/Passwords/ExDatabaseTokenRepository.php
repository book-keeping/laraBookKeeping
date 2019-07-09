<?php

namespace App\Framework\Auth\Passwords;

use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Carbon;

class ExDatabaseTokenRepository extends DatabaseTokenRepository
{
    /**
     * The token database index name.
     *
     * @var string
     */
    protected $index_name;

    /**
     * Create a new token repository instance.
     *
     * @param \Illuminate\Database\ConnectionInterface $connection
     * @param \Illuminate\Contracts\Hashing\Hasher     $hasher
     * @param string                                   $table
     * @param string                                   $hashKey
     * @param string                                   $index_name
     * @param int                                      $expires
     *
     * @return void
     */
    public function __construct(ConnectionInterface $connection, HasherContract $hasher,
                                $table, $hashKey, $index_name, $expires = 60)
    {
        $this->table = $table;
        $this->hasher = $hasher;
        $this->hashKey = $hashKey;
        $this->expires = $expires * 60;
        $this->connection = $connection;
        $this->index_name = $index_name;
    }

    /**
     * Create a new token record.
     *
     * @param Illuminate\Contracts\Auth\CanResetPassword $user
     *
     * @return string
     */
    public function create(CanResetPasswordContract $user)
    {
        $index = $user->getIndexForPasswordReset($this->index_name);

        $this->deleteExisting($user);

        // We will create a new, random token for the user so that we can e-mail them
        // a safe link to the password reset form. Then we will insert a record in
        // the database so that we can verify the token within the actual reset.
        $token = $this->createNewToken();

        $this->getTable()->insert($this->getPayload($index, $token));

        return $token;
    }

    /**
     * Delete all existing reset tokens from the database.
     *
     * @param Illuminate\Contracts\Auth\CanResetPassword $user
     *
     * @return int
     */
    protected function deleteExisting(CanResetPasswordContract $user)
    {
        return $this->getTable()->where($this->index_name, $user->getIndexForPasswordReset($this->index_name))->delete();
    }

    /**
     * Build the record payload for the table.
     *
     * @param string $index
     * @param string $token
     *
     * @return array
     */
    protected function getPayload($index, $token)
    {
        return [$this->index_name => $index, 'token' => $this->hasher->make($token), 'created_at' => new Carbon()];
    }

    /**
     * Determine if a token record exists and is valid.
     *
     * @param Illuminate\Contracts\Auth\CanResetPassword $user
     * @param string                                     $token
     *
     * @return bool
     */
    public function exists(CanResetPasswordContract $user, $token)
    {
        $record = (array) $this->getTable()->where(
            $this->index_name, $user->getIndexForPasswordReset($this->index_name)
        )->first();

        return $record &&
               !$this->tokenExpired($record['created_at']) &&
                 $this->hasher->check($token, $record['token']);
    }
}
