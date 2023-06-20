<?php

namespace App\Http\Controllers;

// use App\Submission;
use App\Http\Controllers\BaseController\BaseController;
use App\Models\ProgramAdmin;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProgramAdminController extends BaseController
{
    public function index(Request $request)
    {
        $query = ProgramAdmin::query()->with(['program', 'user']);
        $result = array();
        $result['data'] = array();
        $user = Auth()->user();
        try {

            // $query = $query->where('user_id', $user->id)
            //     ->orWhere('user_id', $user->supervisor_id);
            if ($user->roles === 'GSBH') {
                $query = $query->Where('user_id', $user->id);

            } elseif ($user->roles === 'QLGS') {

            } else {
                // For other roles, show program admins based on user ID only
                $query = $query->where('user_id', $user->supervisor_id);
            }
            if ($request->filled('program_id') && $request->program_id !== 'null' && $request->program_id !== 'undefined') {
                $query = $query->where('program_id', $request->program_id);

            }
            $program_admin = $query->orderByDesc('created_at')->get();
            $result['data'] = $program_admin;
            $result['GSBH'] = ($user->roles === 'GSBH');
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
            'program_id.required' => 'Chương trình không được để trống',
            'user_id.required' => 'Admin không được để trống',
        ];
        $validator = Validator::make($request->all(), [
            'program_id' => 'required',
            'user_id' => 'required',
        ], $messages);

        $failed = $validator->fails();
        $fields = $request->all();
        $isErr = false;
        foreach ($fields['user_id'] as $user_id) {
            $program_admin = ProgramAdmin::where('program_id', $fields['program_id'])
                ->where('user_id', $user_id)
                ->first();
            if ($program_admin) {
                $validator->errors()->add('user_id', $program_admin->user->name . ' đã được thêm vào chương trình này');
                $isErr = true;
            }
        }
        if ($failed || $isErr) {
            $result['data']['errors'] = $validator->errors();
        } else {

            try {
                foreach ($fields['user_id'] as $user_id) {
                    $re = ProgramAdmin::create([
                        'program_id' => $fields['program_id'],
                        'user_id' => $user_id,
                    ]);
                }
                $result['data']['data'] = $re;
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
        $messages = [
            'program_id.required' => 'Chương trình không được để trống',
            'user.required' => 'Admin không được để trống',
        ];
        $validator = Validator::make($request->all(), [
            'program_id' => 'required',
            'user' => 'required',
        ], $messages);
        $fields = $request->all();
        $failed = $validator->fails();
        $isErr = false;

        $program_admin = ProgramAdmin::where('program_id', $fields['program_id'])
            ->where('user_id', $fields['user'])
            ->where('id', '!=', $id)
            ->first();
      
        if ($program_admin) {
            $validator->errors()->add('user', $program_admin->user->name . ' đã được thêm vào chương trình này');
            $isErr = true;
        }
        if ($failed || $isErr) {
            $result['data']['errors'] = $validator->errors();
        } else {
            try {
                $program_admin = ProgramAdmin::findOrFail($id);
                $program_admin->program_id = $request->input('program_id');
                $program_admin->user_id = $request->input('user');
                $program_admin->save();
                if ($program_admin) {
                    $result['data']['success'] = 1;
                    $result['data'] = $program_admin;
                }
            } catch (\Exception $e) {
                $result['data']['errors'] = $e->getMessage();
            }

        }

        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);
    }
    public function destroy(Request $request, $id)
    {
        $result = array();
        $result['data'] = array();
        $result['data']['success'] = 0;
        $program_admin = ProgramAdmin::findOrFail($id);
        if ($program_admin) {
            $program_admin->delete();
            $result['data']['success'] = 1;
        }
        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);
    }
}
