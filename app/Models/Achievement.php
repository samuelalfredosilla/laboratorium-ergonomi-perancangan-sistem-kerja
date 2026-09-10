<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Achievement extends Model
{
    use HasFactory;

    protected $table = 'achievements';

    protected $fillable = [
        'title',
        'level',
        'achiever_name',
        'date_achieved',
        'is_active',
        'description',
        'photo',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'date_achieved' => 'date:Y-m-d',
    ];

    protected $appends = [
        'photo_url',
    ];

    /**
     * Accessor untuk mendapatkan URL foto dengan aman
     */
    public function getPhotoUrlAttribute(): string
    {
        if (! $this->photo) {
            // Gambar default jika tidak ada foto (sesuaikan dengan path Anda)
            return asset('images/1.jpeg'); 
        }

        return Str::startsWith($this->photo, 'http') ? $this->photo : asset('storage/' . $this->photo);
    }

    /**
     * Relasi ke Riwayat Perubahan (Activity Log)
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'subject_id')
            ->where('subject_type', self::class)
            ->latest();
    }
}