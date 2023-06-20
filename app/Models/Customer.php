<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Customer extends Model
{
    protected $fillable = [
        'dms_code',
        'name',
        'address',
        'user_id'
    ];


    public function programs()
    {
        return $this->belongsToMany(Program::class, 'program_customers');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
