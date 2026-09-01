<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaboratoryEvent extends Model
{
    use HasFactory;

    protected $table = 'laboratory_events';

    protected $fillable = [
        'title',
        'description',
        'photo',
        'event_date',
        'event_time',
        'event_place',
        'gdrive_link',
        'uploaded_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'uploaded_at' => 'datetime',
    ];

    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/' . $this->photo) : null;
    }
}
