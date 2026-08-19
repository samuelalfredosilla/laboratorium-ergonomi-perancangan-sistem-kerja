<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject_type',
        'subject_id',
        'action',
        'description',
        'properties',
        'ip_address',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper statis untuk mencatat riwayat aktivitas secara instan
     */
    public static function record($subject, string $action, string $description, array $properties = []): self
    {
        return self::create([
            'user_id'      => auth()->id(),
            'subject_type' => get_class($subject),
            'subject_id'   => $subject->id ?? null,
            'action'       => $action,
            'description'  => $description,
            'properties'   => !empty($properties) ? $properties : null,
            'ip_address'   => request()->ip(),
        ]);
    }
}
