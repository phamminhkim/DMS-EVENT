<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Program;
use App\Models\ProgramAdmin;
use App\Models\ProgramStage;
use App\Models\Customer;
use App\Models\SubmissionImage;
use App\User;

class Submission extends Model
{
    protected $fillable = [
        'program_id',
        'customer_id',
        'user_id',
        'note',
        'feedback_content',
        'feedback_at',
        'feedback_by',
        'submission_date',
        'is_approved_level2',
        'is_rejected_level2',
        'image_count',
        'send_date',
        'status',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function program_admin()
    {
        return $this->belongsTo(ProgramAdmin::class, 'program_id');
    }

    public function program_stage()
    {
        return $this->belongsTo(ProgramStage::class, 'program_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function submissionImages()
    {
        return $this->hasMany(SubmissionImage::class);
    }
    
    public function feedback()
    {
        return $this->belongsTo(User::class, 'feedback_by');
    }

}
