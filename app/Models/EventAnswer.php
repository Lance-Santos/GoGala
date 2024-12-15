<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAnswer extends Model
{
    use HasFactory;

    // The table associated with the model
    protected $table = 'event_answers';

    // The attributes that are mass assignable
    protected $fillable = [
        'event_questions_id', // Question being answered
        'user_id',             // User who answered
        'answer_text',         // The answer content
    ];

    // Relationship: An answer belongs to a question
    public function question()
    {
        return $this->belongsTo(EventQuestion::class, 'event_questions_id');
    }

    // Relationship: An answer belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
