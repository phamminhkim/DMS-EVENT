<?php

namespace App\Http\Controllers;

// use App\Submission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;
use App\Http\Controllers\BaseController\BaseController;
use App\User;
use App\Imports\CustomerImport;
use Maatwebsite\Excel\Facades\Excel;

class CustomerExcelController extends BaseController
{
   public function store(Request $request)
    {
        try {
            $file = $request->file('file');
           
            $demo = Excel::import(new CustomerImport, $file);
        } catch (\Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => $e->getMessage(),
            ]);
            exit();
        }
    }
}