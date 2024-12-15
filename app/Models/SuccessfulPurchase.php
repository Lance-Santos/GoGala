<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuccessfulPurchase extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'event_id','ticket_id', 'total_price', 'qr_code', 'is_free','is_successful','transaction_type','type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    public function ticket()
    {
        return $this->belongsTo(Tickets::class);
    }
}
