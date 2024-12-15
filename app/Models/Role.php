<?php

// app/Models/Role.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    // Specify the table if it's not the plural of the model name
    protected $table = 'roles';

    // Specify the fillable fields
    protected $fillable = ['name', 'description'];
}
