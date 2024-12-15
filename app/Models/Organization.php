<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    // Define the table associated with the model (optional if following Laravel conventions)
    protected $table = 'organizations';

    // Specify the attributes that are mass assignable
    protected $fillable = [
        'user_id',
        'organization_name',
        'organization_slug',
        'organization_bio',
        'organization_email',
        'img_url_profile',
        'img_url_background',
    ];

    // Define any relationships (if applicable)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function events()
    {
        return $this->hasMany(Event::class, 'organization_id');
    }
    // Additional methods can be added here for business logic
}
