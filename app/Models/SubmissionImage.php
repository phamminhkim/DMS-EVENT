<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;


class SubmissionImage extends Model
{
    protected $fillable = [
        'submission_id',
        'user_id',
        'image_path',
        'is_approved_level2',
        'is_rejected_level2',
        'feedback_content',
        'send_date',
        'program_stage_id',
        'image_id',
        'image_count',
        
        
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
    public function files()
    {
        return $this->hasMany(file::class, 'fileable_id');
    }
    public function program_stage()
    {
        return $this->belongsTo(ProgramStage::class);
    }
    public function feedback()
    {
        return $this->belongsTo(User::class, 'feedback_by');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
