<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class file extends Model
{
    protected $fillable = [
        'fileable_id',
        'fileable_type',
        'user_id',
        'url',
        'name',
        'file_type'
    ];
}
