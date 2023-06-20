<?php

namespace App\Http\Controllers;

// use App\Submission;
use App\Http\Controllers\BaseController\BaseController;
use App\Models\file;
use App\Models\Program;
use App\Models\ProgramAdmin;
use Intervention\Image\Facades\Image;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProgramController extends BaseController
{
    public function index(Request $request)
    {

        $query = Program::query()->with(['user', 'files','images']);
        $result = array();
        $result['data'] = array();
        $user = Auth()->user();
        try {
            if ($user->roles == "GSBH") {
                $query = $query->wherehas('admins', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            } else if ($user->roles == "QLGS") {

            } else {
                $query = $query->wherehas('admins', function ($q) use ($user) {
                    $q->where('user_id', $user->supervisor_id);
                });
            }

            // $ids = User::where("supervisor_id", $user->id)->get()->pluck('id')->flatten();

            // $query = $query->where('user_id', $user->id);
            $programs = $query->get();
            $result['data'] = $programs;
            $result['success'] = "1";

        } catch (Exception $e) {
            $result['data']['errors'] = $e->getMessage();
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);
    }

    public function store(Request $request)
    {
        $result = array();
        $result['data'] = array();
        $result['data']['success'] = 0;
        date_default_timezone_set("Asia/Ho_Chi_Minh");

        $messages = [
            'name.required' => 'Tên chương trình không được để trống',
            'start_date.required' => 'Ngày bắt đầu không được để trống',
            'end_date.required' => 'Ngày kết thúc không được để trống',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',

        ], $messages);

        $failed = $validator->fails();
        $isErr = false;
        $fields = $request->all();

        if ($request->dms_code) {
            $dep_temp = Program::where('dms_code', $request->dms_code)
                ->where('dms_code', $request->dms_code)->first();

            if ($dep_temp) {
                $result['data']['message'] = "Trùng mã, vui lòng nhập mã khác";
                $validator->errors()->add('dms_code', 'Trùng mã, vui lòng nhập mã khác');
                $isErr = true;
            }
        }

        if ($failed || $isErr) {
            $result['data']['errors'] = $validator->errors();
        } else {
            try {

                // $user_id = new User();
                $user_id = Auth()->user()->id;
                $fields['user_id'] = $user_id;

                $re = Program::create($fields);
                if ($re) {
                    $files = $fields['files'];
                    for ($i = 0; $i < count($files); $i++) {
                        $file = new file();
                        $file->name = $files[$i]["name"];
                        $ext = substr($files[$i]["name"], strrpos($files[$i]["name"], '.') + 1);
                        $name = "program_files/" . uniqid() . '.' . $ext; // $attachment_file[$i]["ext"];
                        $file->url = $name;
                        $file->file_type = 'file';
                        $file->fileable_id = $re->id;
                        $file->fileable_type = Program::class;
                        $file->user_id = $user_id;
                        $file->save();
                        $base64_str = substr($files[$i]['base64'], strpos($files[$i]['base64'], ",") + 1);
                        $image = base64_decode($base64_str);
                        Storage::disk('public')->put($name, $image);

                    }
                    $images = $fields['images'];
                    foreach ($images as $image) {
                        $randomName = uniqid(); // Tạo tên ngẫu nhiên
                        $imagePath = 'program_images/' . $randomName;
                    
                        // Lấy dữ liệu base64 từ image_path
                        $base64Data = substr($image['url'], strpos($image['url'], ",") + 1);
                    
                        $imageData = base64_decode($base64Data);
                        // Ghi dữ liệu vào tệp
                        $fileWriteResult = file_put_contents($imagePath, $imageData);
                    
                        $imageExtension = pathinfo($image['name'], PATHINFO_EXTENSION); // Lấy đuôi tệp từ tên gốc
                        $imagePathWithExtension = $imagePath . '.' . $imageExtension; // Tạo tên tệp với đuôi
                     
                    
                        // Giải nén ảnh và chuyển đổi thành WebP
                        $image_webp = Image::make($imageData);
                    
                        $image_webp->save($imagePathWithExtension, 50, 'webp');
                    
                        // Đổi tên tệp thành tên với đuôi WebP
                        $newImagePathWithExtension = $imagePath . '.webp';
                        rename($imagePathWithExtension, $newImagePathWithExtension);
                    
                        file::create([
                            'fileable_id' => $re->id,
                            'fileable_type' => Program::class,
                            'user_id' => $user_id,
                            'url' => $newImagePathWithExtension,
                            'name' => $image['name'],
                            'file_type' => 'image'
                        ]);
                    }
                    $result['data'] = $re;
                    $result['data']['success'] = 1;
                }
            } catch (\Exception $e) {
                $result['data']['errors'] = $e->getMessage();
            }
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function show($id)
    {
        $programs = Program::findOrFail($id);

        $result = array();
        $result['data'] = array();
        $result['data'] = $programs;
        $result['success'] = "1";

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function update(Request $request, $id)
    {

        $result = array();
        $result['data'] = array();
        $result['data']['success'] = 0;
        date_default_timezone_set("Asia/Ho_Chi_Minh");

        $messages = [
            'name.required' => 'Tên chương trình không được để trống',
            'start_date.required' => 'Ngày bắt đầu không được để trống',
            'end_date.required' => 'Ngày kết thúc không được để trống',
        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ], $messages);

        $fields = $request->all();
        $failed = $validator->fails();
        $isErr = false;

        if ($failed || $isErr) {
            $result['data']['errors'] = $validator->errors();
        } else {
            try {
                $user_id = Auth()->user()->id;
                $programs = Program::findOrFail($id);
                $programs->dms_code = $request->input('dms_code');
                $programs->name = $request->input('name');
                $programs->start_date = $request->input('start_date');
                $programs->end_date = $request->input('end_date');
                $programs->note = $request->input('note');
                if ($programs->save()) {
                    $files = $fields['files'];
                    
                    foreach ($files as  $file) {
                        if(isset($file['id']) && !empty($file['id']) ){
                            $file_update = file::find($file['id']);
                            $file_update->name = $file['name'];
                            $file_update->save();
                        }else{
                            $file_store = new file();
                            $file_store->name = $file['name'];       
                            $ext = substr($file['name'], strrpos($file['name'], '.') + 1);
                            $name = "program_files/" . uniqid() . '.' . $ext; // $attachment_file[$i]["ext"];
                            $file_store->url = $name;
                            $file_store->file_type = 'file';
                            $file_store->fileable_id = $programs->id;
                            $file_store->fileable_type = Program::class;
                            $file_store->user_id = Auth()->user()->id;
                            $file_store->save();
                            $base64_str = substr($file['base64'], strpos($file['base64'], ",") + 1);
                            $image = base64_decode($base64_str);
                            Storage::disk('public')->put($name, $image);
                        }
                    }

                    $file_dels = $fields['file_dels'];
                    foreach ($file_dels as  $file_del) {
                        $file_del = file::find($file_del['id']);
                        if($file_del->delete()){
                            Storage::disk('public')->delete($file_del->url);
                        }
                    }
                    $imgaes = $fields['images'];
                    foreach ($imgaes as $image) {
                        // Kiểm tra nếu có trường "id" trong phần tử
                        if (isset($image['id']) && !empty($image['id'])) {
                        } else {
                            $randomName = uniqid(); // Tạo tên ngẫu nhiên
                            $imagePath = 'program_images/' . $randomName;
                        
                            // Lấy dữ liệu base64 từ image_path
                            $base64Data = substr($image['url'], strpos($image['url'], ",") + 1);
                        
                            $imageData = base64_decode($base64Data);
                            // Ghi dữ liệu vào tệp
                            $fileWriteResult = file_put_contents($imagePath, $imageData);
                        
                            $imageExtension = pathinfo($image['name'], PATHINFO_EXTENSION); // Lấy đuôi tệp từ tên gốc
                            $imagePathWithExtension = $imagePath . '.' . $imageExtension; // Tạo tên tệp với đuôi
                         
                        
                            // Giải nén ảnh và chuyển đổi thành WebP
                            $image_update = Image::make($imageData);
                        
                            $image_update->save($imagePathWithExtension, 50, 'webp');
                        
                            // Đổi tên tệp thành tên với đuôi WebP
                            $newImagePathWithExtension = $imagePath . '.webp';
                            rename($imagePathWithExtension, $newImagePathWithExtension);
                            file::create([
                                'fileable_id' => $programs->id,
                                'fileable_type' => Program::class,
                                'user_id' => $user_id,
                                'url' => $newImagePathWithExtension,
                                'name' => $randomName . '.' . $imageExtension,
                                'file_type' => 'image'
                            ]);         
                        }
                    }

                    $image_dels = $fields['image_dels'];
                    foreach ($image_dels as  $image_del) {
                        $image = file::find($image_del['id']);
                        if (isset($image_del['id']) && $image_del['id'] !== "") {
                            $imagePath = public_path($image->url);

                            if (file_exists($imagePath)) {
                                unlink($imagePath);
                            }
                            $image->delete();
                        }
                    }


                    $result['data']['success'] = 1;
                    $result['data']['data'] = $programs;
                }
            } catch (\Exception $e) {
                $result['data']['errors'] = $e->getMessage();
            }
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function destroy($id)
    {

        $result = array();
        $result['data'] = array();
        $result['data']['success'] = 0;
        $validator = Validator::make(['id' => $id], []);
        $isErr = false;
        $check = ProgramAdmin::where('program_id', $id)->first();
        if ($check) {
            $validator->errors()->add('id', 'Không thể xóa chương trình này, vì đã có người quản lý, vui lòng liên hệ admin để xóa');
            $isErr = true;
        }
        if ($isErr) {
            $result['data']['errors'] = $validator->errors();
        } else {
            try {
                $deleteProgram = Program::with(['files','images'])->findOrFail($id); 
                foreach ($deleteProgram->files as $file) {
                     $file->delete();
                    Storage::disk('public')->delete($file->url);
                }
                foreach ($deleteProgram->images as $image) {
                    $imagePath = public_path($image->url);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                    $image->delete();
                }
                if ($deleteProgram->delete()) {
                    $result['data']['success'] = 1;
                }
            } catch (\Exception $e) {
                $result['data']['errors'] = $e->getMessage();
            }
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function show_join_program(Request $request)
    {
        $result = array();
        $result['data'] = array();
        $result['data']['success'] = 0;

        try {
            $user = Auth()->user();
            $query = ProgramAdmin::query()->with(['program', 'user', 'program_stages']);
            if ($user->roles === 'GSBH') {
                $query = $query->Where('user_id', $user->id);

            } else {
                // For other roles, show program admins based on user ID only
                $query = $query->where('user_id', $user->supervisor_id);
            }

            $programs = $query->get();
            $result['data'] = $programs;
            $result['success'] = "1";

        } catch (Exception $e) {
            $result['data']['errors'] = $e->getMessage();
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);
    }

}
