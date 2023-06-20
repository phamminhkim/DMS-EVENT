<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use App\Models\file;
use App\Models\ProgramStage;
use App\Models\Submission;
use App\Models\SubmissionImage;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubmissionImageUploadController extends Controller
{
    public function index(Request $request)
    {
        $query = SubmissionImage::query()->with(['user', 'feedback', 'program_stage', 'submission', 'files', 'submission.program', 'submission.customer', 'submission.user']);
        $result = array();
        $result['data'] = array();
        $user = Auth()->user();
        try {
            $query = $query->where(function ($query) use ($user) {
                if ($user->roles === 'GSBH') {
                    // If the user has the role 'GSBH', show all submissions of their employees
                    $query->orWhere('user_id', $user->id)
                        ->orWhereHas('user', function ($query) use ($user) {
                            $query->where('supervisor_id', $user->id);
                        });
                } else if ($user->roles === 'QLGS') {

                } else {
                    $query->orWhere(function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    });
                }
            });
            if ($request->filled('customer_id')) {
                $query = $query->whereHas('submission', function ($query) use ($request) {
                    $query->where('customer_id', $request->customer_id);
                });
            }
            if ($request->filled('program_id') && $request->program_id !== 'null' && $request->program_id !== 'undefined') {
                $query = $query->whereHas('submission', function ($query) use ($request) {
                    $query->where('program_id', $request->program_id);
                });
            }
            if ($request->filled('status')) {
                if ($request->status == 'is_approved') {
                    $query->where('is_approved_level2', 1);
                }
                if ($request->status == 'is_rejected') {
                    $query->where('is_rejected_level2', 1);
                }
                if ($request->status == 'is_pending') {
                    $query->where(function ($query) use ($request) {
                        $query->where('is_approved_level2', 0)
                            ->where('is_rejected_level2', 0);
                    });
                }
            }
            if ($request->filled('query')) {
                $query->where(function ($query) use ($request) {
                    $query->whereHas('submission.customer', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->input('query') . '%');
                    })->orWhereHas('submission.program', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->input('query') . '%');
                    });
                });
            }
            if($request->filled('user_GSBH') && $request->user_GSBH !== 'null' && $request->user_GSBH !== 'undefined'){
                $query = $query->where('feedback_by', $request->user_GSBH);
            }

            $submissions = $query->orderByDesc('created_at')->get();
            $result['data'] = $submissions;
            $result['staff'] = $user->supervisor_id !== null && $user->roles === null ? true : false;
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

        $messages = [
            'submission_id.required' => 'Không được để trống',
            'program_stage_id.required' => 'Không được để trống',
        ];
        $validator = Validator::make($request->all(), [
            'submission_id' => 'required',
            'program_stage_id' => 'required',

        ], $messages);

        $failed = $validator->fails();
        $fields = $request->all();
        $isErr = false;

        $duplicate = SubmissionImage::where('submission_id', $fields['submission_id'])
            ->where('program_stage_id', $fields['program_stage_id'])->where('is_rejected', 0)->where('is_rejected_level2', 0)->first();
        $program_stage = ProgramStage::find($fields['program_stage_id']);
        if ($duplicate) {
            $validator->errors()->add('submission_id', 'Đã tham gia vào đợt ' . $program_stage->stage);
            $isErr = true;
        }
        $checkSubmission = Submission::where('id', $fields['submission_id'])->with(['program'])->first();
        if ($checkSubmission->program_id !== $program_stage->program_id) {
            $validator->errors()->add('submission_id', 'Chọn đợt đánh giá không thuộc chương trình ' . $checkSubmission->program->name . ' hiện tại');
            $isErr = true;
        }
        if ($program_stage->status == 0) {
            $validator->errors()->add('program_stage_id', 'Đợt đánh giá ' . $program_stage->stage . ' đã đóng');
            $isErr = true;
        }

        if ($failed || $isErr) {
            $result['data']['errors'] = $validator->errors();
        } else {

            try {
                $user = Auth()->user();

                $submission_image = SubmissionImage::create([
                    'submission_id' => $fields['submission_id'],
                    'program_stage_id' => $fields['program_stage_id'],
                    'user_id' => $user->id,
                ]);
                if ($submission_image) {
                    $submisstion_id = $fields['submission_id'];
                    $program_stage_id = $fields['program_stage_id'];

                    // Đếm số lượng submission có cùng program_id và customer_id
                    $submissionCount = SubmissionImage::where('submission_id', $submisstion_id)
                        ->where('program_stage_id', $program_stage_id)->count();
                    // Kiểm tra nếu số lượng submission là 2 hoặc hơn
                    if ($submissionCount >= 2) {
                        $newSubmission = SubmissionImage::find($submission_image->id);
                        if ($newSubmission) {
                            $newSubmission->image_count = $submissionCount;
                            $newSubmission->save();
                        }
                    }
                    $imageURLs = $fields['images'];
                    foreach ($imageURLs as $imageURL) {
                        $randomName = uniqid(); // Tạo tên ngẫu nhiên
                        $imagePath = 'submission_images/' . $randomName;
                    
                        // Lấy dữ liệu base64 từ image_path
                        $base64Data = substr($imageURL['url'], strpos($imageURL['url'], ",") + 1);
                    
                        $imageData = base64_decode($base64Data);
                        // Ghi dữ liệu vào tệp
                        $fileWriteResult = file_put_contents($imagePath, $imageData);
                    
                        $imageExtension = pathinfo($imageURL['name'], PATHINFO_EXTENSION); // Lấy đuôi tệp từ tên gốc
                        $imagePathWithExtension = $imagePath . '.' . $imageExtension; // Tạo tên tệp với đuôi
                     
                    
                        // Giải nén ảnh và chuyển đổi thành WebP
                        $image = Image::make($imageData);
                    
                        $image->save($imagePathWithExtension, 50, 'webp');
                    
                        // Đổi tên tệp thành tên với đuôi WebP
                        $newImagePathWithExtension = $imagePath . '.webp';
                        rename($imagePathWithExtension, $newImagePathWithExtension);
                    
                        file::create([
                            'fileable_id' => $submission_image->id,
                            'fileable_type' => SubmissionImage::class,
                            'user_id' => $user->id,
                            'url' => $newImagePathWithExtension,
                            'name' => $imageURL['name'],
                        ]);
                    }
                    
                }

                $result['data']['data'] = $submission_image;
                $result['data']['success'] = 1;

            } catch (\Exception $e) {
                $result['data']['errors'] = $e->getMessage();
            }
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);
    }
    public function update(Request $request, $id)
    {
        $result = array();
        $result['data'] = array();
        $result['data']['success'] = 0;
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $validator = Validator::make($request->all(), [

        ]);
        $failed = $validator->fails();
        $fields = $request->all();
        $isErr = false;

        $duplicate = SubmissionImage::where('submission_id', $fields['submission_id'])
            ->where('program_stage_id', $fields['program_stage_id'])->where('is_rejected', 0)->where('is_rejected_level2', 0)->where('id', '!=', $id)->first();
        $program_stage = ProgramStage::find($fields['program_stage_id']);
        if ($duplicate) {

            $validator->errors()->add('submission_id', 'Đã tham gia vào đợt ' . $program_stage->stage);
            $isErr = true;
        }
        $checkSubmission = Submission::where('id', $fields['submission_id'])->with(['program'])->first();
        if ($checkSubmission->program_id !== $program_stage->program_id) {
            $validator->errors()->add('program_stage_id', 'Chọn đợt đánh giá không thuộc chương trình ' . $checkSubmission->program->name . ' hiện tại');
            $isErr = true;

        }
        if ($program_stage->status == 0) {
            $validator->errors()->add('program_stage_id', 'Đợt đánh giá ' . $program_stage->stage . ' đã đóng');
            $isErr = true;
        }

        if ($failed || $isErr) {
            $result['data']['errors'] = $validator->errors();
        } else {
            try {
                $user = Auth()->user();
                // $submissionCount = SubmissionImage::where('submission_id', $fields['submission_id'])
                //     ->where('program_stage_id', $fields['program_stage_id'])
                //     ->count();
                // dd($submissionCount);

                // if ($submissionCount >= 2) {
                //     $submission->image_count = $submissionCount;
                // }
                $submission = SubmissionImage::findOrFail($id);
                $submission->update([
                    'submission_id' => $fields['submission_id'],
                    'program_stage_id' => $fields['program_stage_id'],
                    'user_id' => $user->id,
                ]);
                // Đếm số lượng submission có cùng program_id và customer_id
                $submissionCount = SubmissionImage::where('submission_id', $fields['submission_id'])
                    ->where('program_stage_id', $fields['program_stage_id'])
                    ->count();

                $submission->image_count = $submissionCount;
                $submission->save();

                if ($submission) {
                    if (isset($fields['image_dels'])) {
                        $image_dels = $fields['image_dels'];
                        foreach ($image_dels as $image_del) {
                            $image = file::find($image_del['id']);
                            if (isset($image_del['id']) && $image_del['id'] !== "") {
                                $imagePath = public_path($image->url);

                                if (file_exists($imagePath)) {
                                    unlink($imagePath);
                                }
                                $image->delete();
                            }
                        }
                    }
                    if (isset($fields['images'])) {

                        $imageURLs = $fields['images'];

                        foreach ($imageURLs as $imageURL) {
                            // Kiểm tra nếu có trường "id" trong phần tử
                            if (isset($imageURL['id']) && !empty($imageURL['id'])) {
                            } else {
                                $randomName = uniqid(); // Tạo tên ngẫu nhiên
                                $imagePath = 'submission_images/' . $randomName;
                            
                                // Lấy dữ liệu base64 từ image_path
                                $base64Data = substr($imageURL['url'], strpos($imageURL['url'], ",") + 1);
                            
                                $imageData = base64_decode($base64Data);
                                // Ghi dữ liệu vào tệp
                                $fileWriteResult = file_put_contents($imagePath, $imageData);
                            
                                $imageExtension = pathinfo($imageURL['name'], PATHINFO_EXTENSION); // Lấy đuôi tệp từ tên gốc
                                $imagePathWithExtension = $imagePath . '.' . $imageExtension; // Tạo tên tệp với đuôi
                             
                            
                                // Giải nén ảnh và chuyển đổi thành WebP
                                $image = Image::make($imageData);
                            
                                $image->save($imagePathWithExtension, 50, 'webp');
                            
                                // Đổi tên tệp thành tên với đuôi WebP
                                $newImagePathWithExtension = $imagePath . '.webp';
                                rename($imagePathWithExtension, $newImagePathWithExtension);
                                file::create([
                                    'fileable_id' => $submission->id,
                                    'fileable_type' => SubmissionImage::class,
                                    'user_id' => $user->id,
                                    'url' => $newImagePathWithExtension,
                                    'name' => $randomName . '.' . $imageExtension,
                                ]);         
                            }
                        }
                    }
                }
                $result['data']['success'] = 1;
                $result['data']['data'] = $submission;

            } catch (\Exception $e) {
                $result['data']['errors'] = $e->getMessage();
            }

        }
        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);

    }
    public function destroy($id)
    {
        $submission = SubmissionImage::findOrFail($id);
        $result = array();
        $result['data'] = array();
        $result['data']['success'] = 0;
        $submissionImages = file::where('fileable_id', $id)->get();
        try {
            foreach ($submissionImages as $submissionImage) {
                $imagePath = public_path($submissionImage->url);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $submissionImage->delete();
            }
            if ($submission->delete()) {
                $result['data']['success'] = 1;
            }
        } catch (\Exception $e) {
            $result['data']['errors'] = $e->getMessage();
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
    public function send_date(Request $request, $id)
    {
        $result = array();
        $result['data'] = array();
        $result['data']['success'] = 0;
        $validator = Validator::make($request->all(), [
        ]);
        $user_id = new User();
        $user_id = Auth()->user()->id;
        $fields = $request->all();
        $failed = $validator->fails();
        $isErr = false;
        $submission = SubmissionImage::where('id', $id)->first();
        if ($submission->send_date) {
            $validator->errors()->add('send_date', 'Đã gửi duyệt');
            $failed = true;
        }
        if ($failed) {
            $result['data']['errors'] = $validator->errors();
        } else {
            try {
                $submission_send_date = SubmissionImage::where('id', $id)->where('send_date', null)->update([
                    'send_date' => now(),
                ]);
                $result['data']['success'] = 1;
                $result['data'] = $submission_send_date;
            } catch (\Exception $e) {
                $result['data']['errors'] = $e->getMessage();
            }

        }

        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);
    }
    public function image_page(Request $request)
    {
        $perPage = $request->get('per_page', 100);
        $query = SubmissionImage::query()->with(
            [
                'submission.program',
                'submission.customer',
                'submission.user',
            ])
            ->with(['submission' => function ($query) {
                $query->select('id', 'program_id', 'customer_id'); // Chỉ lấy cột program_id và customer_id trong bảng submission
            }])
            ->with(['user' => function ($query) {
                $query->select('id', 'staff_code', 'name'); // Chỉ lấy cột program_id và customer_id trong bảng submission
            }])
            ->with(['program_stage' => function ($query) {
                $query->select('id', 'stage', ); // Chỉ lấy cột program_id và customer_id trong bảng submission
            }])
            ->with(['files' => function ($query) {
                $query->select('id', 'fileable_id', 'url'); // Chỉ lấy cột program_id và customer_id trong bảng submission
            }]);

        $result = array();
        $result['data'] = array();
        $user = Auth()->user();
        try {
            $query = $query->where(function ($query) use ($user) {
                if ($user->roles === 'GSBH') {
                    $query->orWhere('user_id', $user->id)
                        ->orWhereHas('user', function ($query) use ($user) {
                            $query->where('supervisor_id', $user->id);
                        });
                } else if ($user->roles === 'QLGS') {

                } else {
                    $query->orWhere(function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    });
                }
            });
            $query->when($request->filled('customer_id'), function ($query) use ($request) {
                $query->whereHas('submission', function ($query) use ($request) {
                    $query->where('customer_id', $request->customer_id);
                });
            })
                ->when($request->filled('program_id') && $request->program_id !== 'null' && $request->program_id !== 'undefined', function ($query) use ($request) {
                    $query->whereHas('submission', function ($query) use ($request) {
                        $query->where('program_id', $request->program_id);
                    });
                })
                ->when($request->filled('status'), function ($query) use ($request) {
                    switch ($request->status) {
                        case 'is_approved':
                            $query->where('is_approved_level2', 1);
                            break;
                        case 'is_rejected':
                            $query->where(function ($query) {
                                $query->where('is_rejected_level2', 1)
                                    ->orWhere('is_rejected', 1);
                            });
                            break;
                        case 'is_pending':
                            $query->where('is_approved', 1)
                                ->where('is_rejected', 0)
                                ->where('is_rejected_level2', 0)
                                ->where('is_approved_level2', 0);
                            break;
                        case 'is_not_send':
                            $query->whereNull('send_date');
                            break;
                        case 'is_send':
                            $query->whereNotNull('send_date')
                                ->where('is_approved', 0)
                                ->where('is_rejected', 0)
                                ->where('is_rejected_level2', 0)
                                ->where('is_approved_level2', 0);
                            break;
                    }
                })
                ->when($request->filled('query'), function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query->whereHas('submission.customer', function ($query) use ($request) {
                            $query->where('name', 'like', '%' . $request->input('query') . '%');
                        })
                            ->orWhereHas('submission.program', function ($query) use ($request) {
                                $query->where('name', 'like', '%' . $request->input('query') . '%');
                            });
                    });
                })
                ->when($request->filled('stage'), function ($query) use ($request) {
                    $query->whereHas('program_stage', function ($query) use ($request) {
                        $query->where('stage', $request->stage);
                    });
                })
                ->when($request->filled('start_date') && $request->filled('end_date'), function ($query) use ($request) {
                    $start_date = date('Y-m-d 00:00:00', strtotime($request->start_date));
                    $end_date = date('Y-m-d 23:59:59', strtotime($request->end_date));
                    $query->whereBetween('send_date', [$start_date, $end_date]);
                });

            $submissions = $query->orderByDesc('created_at')->paginate($perPage, ['*'], 'page', $request->page);
            $result['data'] = $submissions->items();
            $result['paginate'] = [
                'current_page' => $submissions->currentPage(),
                'last_page' => $submissions->lastPage(),
                'total' => $submissions->total(),
            ];
            $result['staff'] = $user->supervisor_id !== null && $user->roles === null ? true : false;
            $result['success'] = "1";
        } catch (Exception $e) {
            $result['data']['errors'] = $e->getMessage();
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);
    }
    public function count_is_pending(Request $request)
    {
        
        $query = SubmissionImage::query();
        $user = Auth()->user();
      
        $query = $query->where(function ($query) use ($user) {
            if ($user->roles === 'GSBH') {
                // If the user has the role 'GSBH', show all submissions of their employees
                $query->orWhere('user_id', $user->id)
                    ->orWhereHas('user', function ($query) use ($user) {
                        $query->where('supervisor_id', $user->id);
                    });
            } else if ($user->roles === 'QLGS') {

            } else {
                $query->orWhere(function ($query) use ($user) {
                    $query->where('user_id', $user->id);   
                });
            }
        });
        $count = $query->where('is_approved',0)
        ->where('is_rejected',0)
        ->where('is_rejected_level2',0)
        ->where('is_approved_level2',0)
        ->where('send_date','!=',null)->count();

        return response()->json(['count' => $count]);
    }
    public function count_is_pending_level2(Request $request)
    {
        
        $query = SubmissionImage::query();
        $user = Auth()->user();
      
        $query = $query->where(function ($query) use ($user) {
            if ($user->roles === 'GSBH') {
                // If the user has the role 'GSBH', show all submissions of their employees
                $query->orWhere('user_id', $user->id)
                    ->orWhereHas('user', function ($query) use ($user) {
                        $query->where('supervisor_id', $user->id);
                    });
            } else if ($user->roles === 'QLGS') {

            } else {
                $query->orWhere(function ($query) use ($user) {
                    $query->where('user_id', $user->id);   
                });
            }
        });
        $count = $query->where('is_approved',1)
        ->where('is_rejected',0)
        ->where('is_rejected_level2',0)
        ->where('is_approved_level2',0)
        ->where('send_date','!=',null)->count();

        return response()->json(['count' => $count]);
    }
}
