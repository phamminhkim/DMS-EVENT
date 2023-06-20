<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController\BaseController;
use Illuminate\Http\Request;
use App\Models\file;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadFileController extends BaseController
{
    public function download_file($idfile)
    { 
        // Xác định đường dẫn tới file trong thư mục storage
        $file = file::findOrFail($idfile);
        $filepath = $file->url;
       
        // Kiểm tra xem file có tồn tại không
        if (Storage::disk('public')->exists($filepath)) {
            // Tải file về client
            return response()->download(storage_path('app/public/' . $filepath));
        } else {
            // Trả về thông báo lỗi nếu file không tồn tại
            return response()->json(['error' => 'File not found.'], 404);
        }
    }
    
}
