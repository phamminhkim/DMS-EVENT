<?php

namespace App\Http\Controllers;

use App\Exports\SubmissionExportExcel;
use App\Http\Controllers\BaseController\BaseController;
use App\Models\Customer;
use App\Models\Submission;
use App\Models\SubmissionImage;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class SubmissionController extends BaseController
{

    public function index(Request $request)
    {
        $query = Submission::query()->with(['program', 'customer', 'user', 'program.admins', 'feedback']);
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
                        $query->where('user_id', $user->id)
                            ->orWhere('customer_id', $user->id);
                    });
                }
            });

            if ($request->filled('program_id') && $request->program_id !== 'null' && $request->program_id !== 'undefined') {
                $query->where('program_id', $request->program_id);
            }
            if ($request->filled('customer_id') && $request->customer_id !== 'null' && $request->customer_id !== 'undefined') {
                $query->where('customer_id', $request->customer_id);
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
                    $query->whereHas('customer', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->input('query') . '%');
                    })->orWhereHas('program', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->input('query') . '%');
                    });
                });

            }

            $submissions = $query->orderByDesc('created_at')->get();
            $result['data'] = $submissions;
            $result['staff'] = $user->supervisor_id !== null && $user->roles === null ? true : false;
            $result['success'] = "1";

        } catch (Exception $e) {
            $result['data']['errors'] = $e->getMessage();
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result = array();
        $result['data'] = array();
        $result['data']['success'] = 0;

        $messages = [
            'program_id.required' => 'Chương trình không được để trống',
            'customer_id.required' => 'Khách hàng không được để trống',

        ];
        $validator = Validator::make($request->all(), [
            'program_id' => 'required',
            'customer_id' => 'required',

        ], $messages);

        $failed = $validator->fails();
        $fields = $request->all();
        $isErr = false;
        foreach ($fields['customer_id'] as $value) {
            $is_duplicate = Submission::where('program_id', $fields['program_id'])
                ->where('customer_id', $value)
                ->first();
            if ($is_duplicate) {
                $validator->errors()->add('customer_id', 'Khách hàng ' . $is_duplicate->customer->name . ' đã được thêm vào chương trình này');
                $isErr = true;
            }
        }

        if ($failed || $isErr) {
            $result['data']['errors'] = $validator->errors();
        } else {

            try {
                $user_id = new User();
                $user_id = Auth()->user()->id;

                // $fields['user_id'] = $user_id;
                foreach ($fields['customer_id'] as $value) {
                    $re = Submission::create([
                        'program_id' => $fields['program_id'],
                        'customer_id' => $value,
                        'user_id' => $user_id,
                        'note' => $fields['note'],
                    ]);
                }
                // $re = Submission::create($fields);

                // $imageURLs = $fields['images'];

                // foreach ($imageURLs as $imageURL) {
                //     $randomName = uniqid(); // Tạo tên ngẫu nhiên
                //     $imagePath = 'submission_images/' . $randomName;

                //     // Lấy dữ liệu base64 từ image_path
                //     $base64Data = substr($imageURL['image_path'], strpos($imageURL['image_path'], ",") + 1);

                //     $imageData = base64_decode($base64Data);
                //     // Ghi dữ liệu vào tệp
                //     $fileWriteResult = file_put_contents($imagePath, $imageData);

                //     $imageExtension = pathinfo($imageURL['name'], PATHINFO_EXTENSION); // Lấy đuôi tệp từ tên gốc
                //     $imagePathWithExtension = $imagePath . '.' . $imageExtension; // Tạo tên tệp với đuôi

                //     rename($imagePath, $imagePathWithExtension); // Đổi tên tệp thành tên với đuôi

                //     SubmissionImage::create([
                //         'submission_id' => $re->id,
                //         'user_id' => $re->user_id,
                //         'image_path' => $imagePathWithExtension,
                //     ]);
                // }
                $result['data']['data'] = $re;
                $result['data']['success'] = 1;

            } catch (\Exception $e) {
                $result['data']['errors'] = $e->getMessage();
            }
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Submission  $submission
     * @return \Illuminate\Http\Response
     */
    public function show(Submission $submission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Submission  $submission
     * @return \Illuminate\Http\Response
     */
    public function edit(Submission $submission)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Submission  $submission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $result = array();
        $result['data'] = array();
        $result['data']['success'] = 0;
        date_default_timezone_set("Asia/Ho_Chi_Minh");
        $validator = Validator::make($request->all(), [

        ]);
        $fields = $request->all();
        $failed = $validator->fails();
        $isErr = false;
        $is_duplicate = Submission::where('program_id', $fields['program_id'])
            ->where('customer_id', $fields['edit_customer_id'])
            ->where('id', '!=', $id)
            ->first();

        if ($is_duplicate) {
            $validator->errors()->add('customer_id', 'Khách hàng ' . $is_duplicate->customer->name . ' đã được thêm vào chương trình này');
            $isErr = true;
        }
        if ($failed || $isErr) {
            $result['data']['errors'] = $validator->errors();
        } else {
            try {
                $submission = Submission::findOrFail($id);
                $submission->program_id = $request->input('program_id');
                $submission->customer_id = $request->input('edit_customer_id');
                $submission->note = $request->input('note');
                if(  $submission->save()){
                    $result['data']['success'] = 1;
                    $result['data']['data'] = $submission;
    
                }
              
            } catch (\Exception $e) {
                $result['data']['errors'] = $e->getMessage();
            }
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Submission  $submission
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $submission = Submission::findOrFail($id);
        $result = array();
        $result['data'] = array();
        $result['data']['success'] = 0;
        $validator = Validator::make(['id' => $id], []);
        $isErr = false;
        $check = SubmissionImage::where('submission_id', $id)->first();
        if($check){
            $validator->errors()->add('id', 'Không thể xóa do đã có ảnh trong chương trình này, vui lòng xóa items ảnh ở ShowRoom trước khi xóa danh sách đăng ký tham gia này');
            $isErr = true;
        }
        if ($isErr) {
            $result['data']['errors'] = $validator->errors();
        } else {
            try {
                // foreach ($submissionImages as $submissionImage) {
                //     // $imagePath = public_path($submissionImage->image_path);
    
                //     // if (file_exists($imagePath)) {
                //     //     unlink($imagePath);
                //     // }
                //     $submissionImage->delete();
                //     foreach ($submissionImage->files as $file) {
                //         $filePath = public_path($file->url);
    
                //         if (file_exists($filePath)) {
                //             unlink($filePath);
                //         }
                //         $file->delete();
                //     }
                // }
                // Xóa submission
                if ($submission->delete()) {
                    $result['data']['success'] = 1;
                }
            } catch (\Exception $e) {
                $result['data']['errors'] = $e->getMessage();
            }
           
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function is_approved(Request $request, $id)
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
        if ($submission->is_approved == 1 || $submission->is_rejected == 1) {
            $validator->errors()->add('is_approved', 'Đơn đăng ký đã được duyệt hoặc từ chối');
            $failed = true;
        }
        if ($failed) {
            $result['data']['errors'] = $validator->errors();
        } else {
            $submission_approved = SubmissionImage::where('id', $id)->where('is_approved', 0)->update([
                'is_approved' => 1,
                'feedback_by' => $user_id,
                'feedback_at' => now(),
            ]);
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);
    }

    public function is_rejected(Request $request, $id)
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
        if ($submission->is_approved == 1 || $submission->is_rejected == 1) {
            $validator->errors()->add('is_rejected', 'Đơn đăng ký đã được duyệt hoặc từ chối');
            $failed = true;
        }
        if ($failed) {
            $result['data']['errors'] = $validator->errors();
        } else {
            $submission_rejected = SubmissionImage::where('id', $id)->where('is_rejected', 0)
                ->update([
                    'is_rejected' => 1,
                    'feedback_content' => $fields['feedback_content'],
                    'feedback_by' => $user_id,
                    'feedback_at' => now(),
                ]);
            $result['data']['success'] = 1;
            $result['data']['data'] = $submission_rejected;
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);
    }

    public function is_approved_level2(Request $request, $id)
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
        if ($submission->is_approved_level2 == 1 || $submission->is_rejected_level2 == 1) {
            $validator->errors()->add('is_approved_level2', 'Đơn đăng ký đã được duyệt hoặc từ chối');
            $failed = true;
        }
        if ($failed) {
            $result['data']['errors'] = $validator->errors();
        } else {
            $submission_approved = SubmissionImage::where('id', $id)->where('is_approved_level2', 0)->update([
                'is_approved_level2' => 1,
                'feedback_at' => now(),
            ]);
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);
    }

    public function is_rejected_level2(Request $request, $id)
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
        if ($submission->is_approved_level2 == 1 || $submission->is_rejected_level2 == 1) {
            $validator->errors()->add('is_rejected_level2', 'Đơn đăng ký đã được duyệt hoặc từ chối');
            $failed = true;
        }
        if ($failed) {
            $result['data']['errors'] = $validator->errors();
        } else {
            $submission_rejected = SubmissionImage::where('id', $id)->where('is_rejected_level2', 0)
                ->update([
                    'is_rejected_level2' => 1,
                    'feedback_content' => $fields['feedback_content'],
                    'feedback_at' => now(),

                ]);
            $result['data']['success'] = 1;
            $result['data']['data'] = $submission_rejected;
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);
    }

    public function submissionTree()
    {
        // Lấy dữ liệu từ cơ sở dữ liệu
        $submissions = Submission::with('program', 'customer')->get();

        $options = [];
        $programGroups = [];

        foreach ($submissions as $submission) {
            $programId = $submission->program->id;

            if (!in_array($programId, $programGroups)) {
                $programGroups[] = $programId;

                $item = [
                    'id' => $programId . '-program',
                    'label' => $submission->program->name,
                    'children' => [],
                ];

                $options[] = $item;
            }

            $programIndex = array_search($programId, $programGroups);
            $customerItem = [
                'id' => $submission->id,
                'label' => $submission->customer->name,
            ];

            $options[$programIndex]['children'][] = $customerItem;
        }

        return json_encode(['data' => $options], JSON_UNESCAPED_UNICODE);
    }

    public function all_is_approved(Request $request)
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

        if ($failed) {
            $result['data']['errors'] = $validator->errors();
        } else {
            try {
                foreach ($fields as $value) {
                    $submission_approved = SubmissionImage::where('id', $value['id'])->where('is_approved', 0)->update([
                        'is_approved' => 1,
                        'feedback_by' => $user_id,
                        'feedback_at' => now(),
                    ]);
                }
                $result['data']['data'] = $submission_approved;
                $result['data']['success'] = 1;
            } catch (\Throwable $th) {
                $result['data']['errors'] = $th->getMessage();
            }

        }

        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);
    }

    public function all_is_approved_level2(Request $request)
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

        if ($failed) {
            $result['data']['errors'] = $validator->errors();
        } else {
            try {
                foreach ($fields as $value) {
                    $submission_approved = SubmissionImage::where('id', $value['id'])->where('is_approved_level2', 0)->update([
                        'is_approved_level2' => 1,
                        'feedback_at' => now(),
                    ]);
                }
                $result['data']['data'] = $submission_approved;
                $result['data']['success'] = 1;
            } catch (\Throwable $th) {
                $result['data']['errors'] = $th->getMessage();
            }

        }

        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);
    }
    public function submission_register(Request $request, $page)
    {
        $perPage = $request->get('per_page', 10);

        $query = Submission::query()
            ->select('id', 'program_id', 'customer_id')->with(
            [
                'user',
                'feedback',
            ])
            ->with(['program' => function ($query) {
                $query->select('id', 'name', 'dms_code'); // Chỉ lấy cột program_id và customer_id trong bảng submission
            }])
            ->with(['customer' => function ($query) {
                $query->select('id', 'name', 'dms_code'); // Chỉ lấy cột program_id và customer_id trong bảng submission
            }]);

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
                        $query->where('user_id', $user->id)
                            ->orWhere('customer_id', $user->id);
                    });
                }
            });
            if ($request->filled('query')) {
                $query->where(function ($query) use ($request) {
                    $query->whereHas('customer', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->input('query') . '%');
                    })->orWhereHas('program', function ($query) use ($request) {
                        $query->where('name', 'like', '%' . $request->input('query') . '%');
                    });
                });

            }

            $submission = $query->paginate($perPage, ['*'], 'page', $page);
            $result['data'] = $submission->items();
            $result['paginate'] = [
                'current_page' => $submission->currentPage(),
                'last_page' => $submission->lastPage(),
                'total' => $submission->total(),
            ];

            $result['success'] = "1";

        } catch (Exception $e) {
            $result['data']['errors'] = $e->getMessage();
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
    public function export(Request $request)
    {
        try {
            return Excel::download(new SubmissionExportExcel($request->all()), 'submissions.xlsx');
        } catch (\Exception $e) {
            return response()->json([
                'success' => 0,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
