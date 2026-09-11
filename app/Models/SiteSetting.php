<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_email',
        'contact_phone',
        'address',
        'instagram_link',
        'youtube_link',
        'linkedin_link',
        'tiktok_link',
        'logo_url',
    ];
}
