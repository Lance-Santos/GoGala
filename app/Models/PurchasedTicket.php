<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasedTicket extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'event_id',
        'ticket_id',
        'successful_purchases_id',
        'price',
        'is_free',
        'seat_id',
        'seat_identifier',
        'payment_id',
        'is_verified',
        'is_blacklisted',
        'qr_code',
    ];

    /**
     * Get the user that owns the PurchasedTicket.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event associated with the PurchasedTicket.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the ticket associated with the PurchasedTicket.
     */
    public function ticket()
    {
        return $this->belongsTo(Tickets::class);
    }
    public function successful_purchase()
    {
        return $this->belongsTo(SuccessfulPurchase::class, 'successful_purchases_id');
    }
}
