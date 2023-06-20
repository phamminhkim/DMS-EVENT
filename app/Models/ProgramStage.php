<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgramStage extends Model
{
    protected $fillable = [
        'program_id',
        'stage',
        'status',
        'note',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

   
  
}
