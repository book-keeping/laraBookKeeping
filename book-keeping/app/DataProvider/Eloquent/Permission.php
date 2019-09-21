<?php

namespace App\DataProvider\Eloquent;

class Permission extends UuidModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bk2_0_permissions';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'permission_id';
}
