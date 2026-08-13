<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LecturerEducation extends Model
{
    use HasFactory;

    protected $table = 'lecturer_educations';

    protected $fillable = ['lecturer_id', 'degree', 'institution', 'year_range'];

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }
}
