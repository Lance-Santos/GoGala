<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tickets extends Model
{
    use HasFactory;

    // Define the table associated with the model (optional if following Laravel conventions)
    protected $table = 'tickets';

    // Specify the attributes that are mass assignable
    protected $fillable = [
        'event_id',
        'type',
        'price',
        'quantity',
        'isFull',
    ];
    public function events(){
        return $this->belongsTo(Event::class);
    }
}
