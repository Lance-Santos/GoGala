<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
    ];

    // Relationship with Event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Check if a user has already favorited an event
    public static function isFavorited($userId, $eventId)
    {
        return self::where('user_id', $userId)->where('event_id', $eventId)->exists();
    }
    public function isFavoritedByUser($userId)
    {
        return $this->favorites()->where('user_id', $userId)->exists(); 
    }

}
