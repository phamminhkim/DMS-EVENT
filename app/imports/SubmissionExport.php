<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Submission;

class SubmissionsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        dd('kim');
        return Submission::all();
    }
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Description',
            'Created At',
            'Updated At',
        ];
    }

}