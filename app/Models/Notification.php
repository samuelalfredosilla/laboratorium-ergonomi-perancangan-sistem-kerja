<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['message', 'icon', 'color', 'url', 'is_read'];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public static function log(string $message, string $icon = 'fa-bell', string $color = 'maroon', ?string $url = null): self
    {
        return static::create([
            'message' => $message,
            'icon' => $icon,
            'color' => $color,
            'url' => $url,
        ]);
    }
}
