<?php

namespace App\DataProvider\Eloquent;

class SlipGroup extends UuidModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bk2_0_slip_groups';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'slip_group_id';
}
