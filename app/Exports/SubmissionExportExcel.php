<?php

namespace App\Exports;

use App\Models\Customer;
use App\Models\Submission;
use App\Models\SubmissionImage;
use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SubmissionExportExcel implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $requestData;

    public function __construct($requestData)
    {
        $this->requestData = $requestData;
    }
    public function collection()
    {
        $programIds = explode(',', $this->requestData['program_id']);
        $programIds = array_map('intval', $programIds);
        $columns = Submission::with(['user', 'program', 'customer', 'submissionImages' => function ($query) {
            $query->where('is_approved_level2', 1)
                ->orWhere('is_rejected_level2', 1);
        }])
            ->whereIn('program_id', $programIds)->get();
    
        $data = [];
        $index_version_2 = 1;
    
        foreach ($columns as $index => $value) {
            $NVBH = User::where('id', $value->user_id)->first();
            $GSBH = User::where('id', $NVBH->supervisor_id)->first();
    
            $images = $value->submissionImages;
            $distinctStages = $images->pluck('program_stage_id')->unique();
    
            foreach ($distinctStages as $stageId) {
                $stageImages = $images->where('program_stage_id', $stageId);
    
                $row = [
                    'STT' => $index_version_2,
                    'Chương trình' => $value->program->name,
                    'Đợt' => $stageImages->first()->program_stage->stage,
                    'Mã KH' => $value->customer->dms_code,
                    'Tên KH' => $value->customer->name,
                    'Mã NVBH' => $value->user->staff_code,
                    'Tên NVBH' => $value->user->name,
                    'Mã GSBH' => $GSBH->staff_code,
                    'Tên GSBH' => $GSBH->name,
                    'Kết quả cuối cùng' => '', // Initialize last decision column
                    'Tổng số lần Trade duyệt' => 0,
                ];
    
                $i = 1;
                $lastDecision = '';
    
                foreach ($stageImages as $image) {
                    $status = '';
    
                    if ($image->is_approved_level2 == 1) {
                        $status = 'Phê duyệt';
                       
                    } else if ($image->is_rejected_level2 == 1) {
                        $status = 'Từ chối';
                    } else {
                        $status = 'Chưa phê duyệt';
                    }
                    $row['Tổng số lần Trade duyệt']++;
                    $row['Lần ' . $i] = $status;
                    $i++;
    
                    if ($image->is_approved_level2 == 1) {
                        $lastDecision = 'Đạt';
                    } else if ($image->is_rejected_level2 == 1) {
                        $lastDecision = 'Không đạt';
                    } else {
                        $lastDecision = 'Chưa phê duyệt';
                    }
                }
    
                $row['Kết quả cuối cùng'] = $lastDecision;
                $data[] = $row;
                $index_version_2++;
            }
        }
    
        return collect($data);
    }
    

    public function headings(): array
    {
        $data = $this->collection();
        $headings = array_keys($data->first());
    
        return $headings;
    }
    
    
}
