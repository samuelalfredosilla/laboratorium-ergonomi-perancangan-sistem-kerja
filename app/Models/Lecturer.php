<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Lecturer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'nip', 'role', 'photo', 'expertise', 'email',
        'scholar_link', 'linkedin_link', 'sort_order',
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
}
