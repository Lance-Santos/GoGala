<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'events';

    protected $fillable = [
        'organization_id',
        'event_name',
        'event_slug',
        'event_date_start',
        'event_date_end',
        'event_time_start',
        'event_time_end',
        'event_latitude',
        'event_longitude',
        'event_address_string',
        'event_description',
        'event_img_url',
        'event_img_banner_url',
        'event_status',
        'event_type',
        'hasEnded',
    ];

    /**
     * Define casts for date and time attributes.
     */
    protected function casts(): array
    {
        return [
            'event_date_start' => 'date:Y-m-d', // Format as 'YYYY-MM-DD'
            'event_date_end' => 'date:Y-m-d',
            'event_time_start' => 'datetime:H:i:s', // Cast as datetime for storage in 24-hour format
            'event_time_end' => 'datetime:H:i:s',
        ];
    }
    /**
     * Accessors for custom date and time formatting.
     */
    protected function eventDateStart(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->format('Y-m-d')
        );
    }

    protected function eventDateEnd(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->format('Y-m-d')
        );
    }
    protected function eventTimeStart(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->format('h:i A') // 12-hour format with AM/PM
        );
    }
    protected function eventTimeEnd(): Attribute
    {
        return Attribute::make(
            get: fn($value) => Carbon::parse($value)->format('h:i A') // 12-hour format with AM/PM
        );
    }
    /**
     * Relationship with Organization.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    public function tickets()
    {
        return $this->hasMany(Tickets::class,'event_id');
    }
    public function hasTickets()
    {
        return $this->tickets()->exists();
    }
    public function questions()
    {
        return $this->hasMany(EventQuestion::class, 'event_id');
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_event', 'event_id', 'category_id')
        ->withTimestamps();
    }
}
