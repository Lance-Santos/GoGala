<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventQuestion extends Model
{
    use HasFactory;

    // The table associated with the model.
    protected $table = 'event_questions';

    // The attributes that are mass assignable.
    protected $fillable = [
        'event_id',
        'user_id',
        'question',
    ];

    /**
     * Get the event that owns the EventQuestion.
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Get the user that created the EventQuestion.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function answers()
    {
        return $this->hasMany(EventAnswer::class, 'event_questions_id');
    }
}
