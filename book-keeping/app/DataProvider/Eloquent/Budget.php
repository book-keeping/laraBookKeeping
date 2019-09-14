<?php

namespace App\DataProvider\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bk2_0_budgets';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'budget_id';

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
}
