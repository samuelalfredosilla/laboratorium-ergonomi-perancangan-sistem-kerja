<?php

namespace App\Models;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Lecturer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nip',
        'role',
        'photo',
        'expertise',
        'email',
        'scholar_link',
        'linkedin_link',
        'sort_order',
    ];

    protected $appends = ['photo_url'];

    public function educations(): HasMany
    {
        return $this->hasMany(LecturerEducation::class);
    }

    public function researches(): HasMany
    {
        return $this->hasMany(LecturerResearch::class);
    }

    public function communityServices(): HasMany
    {
        return $this->hasMany(LecturerCommunityService::class);
    }

    public function getPhotoUrlAttribute(): string
    {
        if (! $this->photo) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=8D2B2B&color=fff&bold=true';
        }

        return Str::startsWith($this->photo, 'http') ? $this->photo : asset('storage/' . $this->photo);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'subject_id')
            ->where('subject_type', self::class)
            ->latest();
    }

    /**
     * Default pengurutan otomatis di seluruh aplikasi
     */
    protected static function booted(): void
    {
        static::addGlobalScope('ordered', function (Builder $builder) {
            $builder->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
        });
    }
}