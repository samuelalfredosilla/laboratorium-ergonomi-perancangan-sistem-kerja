<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Assistant extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database
     *
     * @var string
     */
    protected $table = 'assistants';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'nim',
        'division',
        'period',
        'is_active_period',
        'email',
        'photo',
        'sort_order',
    ];

    /**
     * Tipe data bawaan (type casting).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active_period' => 'boolean',
        'sort_order'       => 'integer',
    ];

    /**
     * Accessor untuk memanggil foto profil: $assistant->photo_url
     * Otomatis memberi fallback avatar inisial jika foto kosong.
     *
     * @return string
     */
    public function getPhotoUrlAttribute(): string
    {
        if (!$this->photo) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=6B1C1C&color=fff&size=500';
        }

        if (Str::startsWith($this->photo, ['http://', 'https://'])) {
            return $this->photo;
        }

        return asset('storage/' . $this->photo);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'subject_id')
            ->where('subject_type', self::class)
            ->latest();
    }
}
