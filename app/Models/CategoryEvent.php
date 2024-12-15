<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CategoryEvent extends Pivot
{
    // Define the table name if it doesn't follow Laravel's naming convention
    protected $table = 'category_event';

    protected $fillable = [
        'event_id',
        'category_id',
    ];
}
