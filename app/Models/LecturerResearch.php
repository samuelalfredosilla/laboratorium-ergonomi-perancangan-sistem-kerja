<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LecturerResearch extends Model
{
    use HasFactory;

    protected $table = 'lecturer_researches';

    protected $fillable = ['lecturer_id', 'title', 'year'];

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }
}
