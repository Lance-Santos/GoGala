<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfirmedTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'qr_code',
        'is_verified',
        'verified_at'
    ];

    public function event()
    {
        return $this->belongsTo(Events::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
