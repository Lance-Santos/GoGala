<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Specify the fillable fields
    protected $fillable = [
        'role_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'username',
        'email',
        'gender',
        'contact_number',
        'password',
        'bio',
        'profile_img_url',
        'banner_img_url',
    ];

    // Specify the hidden fields
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Specify the casts for any fields that need it
    protected $casts = [
        'email_verified_at' => 'datetime',
        'number_verified_at' => 'datetime',
        'gender' => 'string',
    ];

    // Define a relationship with the Role model
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function organization()
    {
        return $this->hasMany(Organization::class);
    }
    public function purchasedtickets(){
        return $this->hasMany(PurchasedTicket::class,'user_id');
    }
    public function questions()
    {
        return $this->hasMany(EventQuestion::class, 'user_id');
    }
    public function eventAnswers()
    {
        return $this->hasMany(EventAnswer::class);
    }
}
