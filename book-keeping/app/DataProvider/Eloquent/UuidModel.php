<?php

namespace App\DataProvider\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class UuidModel extends Model
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Create a new Eloquent model instance with UUID as primaryKey.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->attributes[$this->primaryKey] = (string) Str::uuid();
    }
}
