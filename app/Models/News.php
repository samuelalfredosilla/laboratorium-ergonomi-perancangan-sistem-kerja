<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';

    protected $fillable = [
        'category_id',
        'user_id',
        'title',
        'slug',
        'image',
        'content',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute(): string
    {
        if (! $this->image) {
            return asset('images/1.jpeg');
        }

        return Str::startsWith($this->image, 'http') ? $this->image : asset('storage/' . $this->image);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
