<?php

namespace App\Models;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'dms_code',
        'name',
        'user_id',
        'start_date',
        'end_date',
        'note'
    ];

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'program_customers');
    }

    public function admins()
    {
        return $this->belongsToMany(User::class, 'program_admins');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function files()
    {
        return $this->hasMany(file::class, 'fileable_id')->where('fileable_type',Program::class)->where('file_type','file');
    }

    public function images()
    {
        return $this->hasMany(file::class, 'fileable_id')->where('fileable_type',Program::class)->where('file_type','image');
    }
}
