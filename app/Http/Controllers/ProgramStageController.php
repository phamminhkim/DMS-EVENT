<?php

namespace App\Http\Controllers;

use App\Models\ProgramStage;
use App\Models\SubmissionImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProgramStageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = ProgramStage::query()->with(['program']);
        $result = array();
        $result['data'] = array();
        $user = Auth()->user();
        try {
            if ($request->filled('status')) {
                $query = $query->where('status', $request->status);
            }
            if ($request->filled('program_id') && $request->program_id !== 'null' && $request->program_id !== 'undefined') {
                $query = $query->where('program_id', $request->program_id);
            }
            if ($request->filled('stage')) {
                $query = $query->where('stage', $request->stage);
            }

            $program_stages = $query->orderByDesc('created_at')->get();
            $result['data'] = $program_stages;
            $result['success'] = "1";

        } catch (Exception $e) {
            $result['data']['errors'] = $e->getMessage();
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);
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
        date_default_timezone_set("Asia/Ho_Chi_Minh");

        $messages = [
            'program_id.required' => 'Chương trình không được để trống',
        ];
        $validator = Validator::make($request->all(), [
            'program_id' => 'required',
        ], $messages);

        $fields = $request->all();
        $failed = $validator->fails();
        $isErr = false;

        if ($request->program_id && $request->stage) {
            $dep_temp = ProgramStage::where('program_id', $request->program_id)
                ->where('stage', $request->stage)->first();

            if ($dep_temp) {
                $result['data']['message'] = "Đợt $request->stage Chương trình này đã tồn tại";
                $validator->errors()->add('stage', "Đợt $request->stage Chương trình này đã tồn tại");
                $isErr = true;
            }

        }

        if ($failed || $isErr) {
            $result['data']['errors'] = $validator->errors();
        } else {
            try {
                $re = ProgramStage::create($fields);
                if ($re) {
                    $result['data'] = $re;
                    $result['data']['success'] = 1;
                }
            } catch (\Exception $e) {
                $result['data']['errors'] = $e->getMessage();
            }
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProgramStage  $programStage
     * @return \Illuminate\Http\Response
     */
    public function show(ProgramStage $programStage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProgramStage  $programStage
     * @return \Illuminate\Http\Response
     */
    public function edit(ProgramStage $programStage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProgramStage  $programStage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $result = array();
        $result['data'] = array();
        $result['data']['success'] = 0;
        date_default_timezone_set("Asia/Ho_Chi_Minh");

        $messages = [
            'program_id.required' => 'Chương trình không được để trống',
        ];
        $validator = Validator::make(
            [
                'id' => $id,
                'program_id' => $request->program_id,
            ], [
                'program_id' => 'required',
            ], $messages);

        $fields = $request->all();
        $failed = $validator->fails();
        $isErr = false;
        if ($request->program_id && $request->stage) {
            $dep_temp = ProgramStage::where('program_id', $request->program_id)
                ->where('stage', $request->stage)->where('id', '!=', $id)->first();
            if ($dep_temp) {
                $result['data']['message'] = "Đợt $request->stage Chương trình này đã tồn tại";
                $validator->errors()->add('stage', "Đợt $request->stage Chương trình này đã tồn tại");
                $isErr = true;
            }
        }
        $check = SubmissionImage::where('program_stage_id', $id)->first();
        if ($check) {
            $result['data']['message'] = "Đợt $request->stage Chương trình này đã tồn tại ảnh, không thể sửa";
            $validator->errors()->add('id', "Đợt $request->stage Chương trình này đã tồn tại ảnh, không thể sửa");
            $isErr = true;
        }
        if ($failed || $isErr) {
            $result['data']['errors'] = $validator->errors();
        } else {
            try {
                $program_stage = ProgramStage::findOrFail($id);
                $program_stage->program_id = $request->input('program_id');
                $program_stage->stage = $request->input('stage');
                $program_stage->note = $request->input('note');

                if ($program_stage->save()) {
                    $result['data']['success'] = 1;
                    $result['data']['data'] = $program_stage;

                }
            } catch (\Exception $e) {
                $result['data']['errors'] = $e->getMessage();
            }

        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProgramStage  $programStage
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $result = array();
        $result['data'] = array();
        $result['data']['success'] = 0;
        $validator = Validator::make(['id' => $id], [
        ]);
        $isErr = false;
        $check = SubmissionImage::where('program_stage_id', $id)->first();
        if ($check) {
            $validator->errors()->add('id', "Đợt này đã có ảnh nộp, không thể xóa");
            $isErr = true;
        }
        if ($isErr) {
            $result['data']['errors'] = $validator->errors();
        } else {
            try {
                $deleteProgram = ProgramStage::findOrFail($id);
                if ($deleteProgram->delete()) {
                    $result['data']['success'] = 1;
                }
            } catch (\Exception $e) {
                $result['data']['errors'] = $e->getMessage();
            }
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
    public function stage_open(Request $request, $id)
    {
        $result = array();
        $result['data'] = array();
        $result['data']['success'] = 0;
        try {
            $open_stage = ProgramStage::where('id', $id)->where('status', 0)->update(['status' => 1]);
            $result['data']['success'] = 1;
        } catch (\Exception $e) {
            $result['data']['errors'] = $e->getMessage();
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
    public function stage_close(Request $request, $id)
    {
        $result = array();
        $result['data'] = array();
        $result['data']['success'] = 0;
        try {
            $stage_close = ProgramStage::where('id', $id)->where('status', 1)->update(['status' => 0]);
            $result['data']['success'] = 1;
        } catch (\Exception $e) {
            $result['data']['errors'] = $e->getMessage();
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function program_stage_open()
    {
        $query = ProgramStage::query()->select('id', 'stage', 'program_id')
            ->with(['program' => function ($query) {
                $query->select('id', 'name');
            }]);
        $result = array();
        $result['data'] = array();
        $user = Auth()->user();
        try {
            $query = $query->where('status', 1);
            $program_stages = $query->get();
            $result['data'] = $program_stages;
            $result['success'] = "1";

        } catch (Exception $e) {
            $result['data']['errors'] = $e->getMessage();
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);
    }

}
