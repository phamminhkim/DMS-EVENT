<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ProgramAdmin extends Model
{
    protected $fillable = [
        'program_id',
        'user_id',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function program_stages()
    {
        return $this->hasMany(ProgramStage::class,'program_id','program_id');
    }

   
}
