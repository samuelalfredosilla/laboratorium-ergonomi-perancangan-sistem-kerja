<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticumTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'gdrive_link',
        'collection_date',
        'collection_time',
        'collection_place',
        'uploaded_at',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    /**
     * Relasi ke Activity Log (Polymorphic atau Model ActivityLog bawaan aplikasi)
     */
    public function activityLogs()
    {
        return $this->morphMany(\App\Models\ActivityLog::class, 'subject')->latest();
    }
}