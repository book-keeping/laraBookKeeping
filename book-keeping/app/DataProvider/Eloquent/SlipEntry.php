<?php

namespace App\DataProvider\Eloquent;

class SlipEntry extends UuidModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bk2_0_slip_entries';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'slip_entry_id';
}
