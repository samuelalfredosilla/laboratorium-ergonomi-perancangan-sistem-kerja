<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabProcedure extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'file_url',
        'sort_order',
    ];
}