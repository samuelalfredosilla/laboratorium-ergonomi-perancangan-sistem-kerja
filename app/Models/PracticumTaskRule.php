<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PracticumTaskRule extends Model
{
    protected $fillable = [
        'practicum_task_id',
        'icon',
        'title',
        'description'
    ];
}
