<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssistantPeriod extends Model
{
    use HasFactory;

    protected $table = 'assistant_periods';

    protected $fillable = [
        'name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function assistants(): HasMany
    {
        return $this->hasMany(Assistant::class, 'period', 'name');
    }
}