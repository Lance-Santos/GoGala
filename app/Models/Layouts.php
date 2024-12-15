<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layouts extends Model
{
    use HasFactory;
    protected $table = "layout";
    protected $fillable = ['data','event_id'];

    // Optionally, if you want to cast the 'data' field back to array automatically
    protected $casts = [
        'data' => 'array',
    ];
}
