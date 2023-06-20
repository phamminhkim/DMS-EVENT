<?php

namespace App\Http\Controllers;

// use App\Submission;
use App\Http\Controllers\BaseController\BaseController;
use App\Models\Customer;
use App\Models\Submission;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $role = $user->roles;

        switch ($role) {
            case "GSBH":
                $ids = User::where("supervisor_id", $user->id)->get()->pluck('id')->flatten();
                $customers = Customer::whereIn('user_id', $ids)->orWhere('user_id', $user->id)->with(['user'])->get();
                break;
            case "QLGS":
                $customers = Customer::with(['user'])->get();
                break;
            default:
                $customers = Customer::where('user_id', auth()->user()->id)->with(['user'])->get();
                break;
        }

        $result = [
            'data' => $customers,
            'staff' => ($user->supervisor_id !== null && $user->roles === null) ? true : false,
            'success' => "1",
        ];

        return json_encode($result, JSON_UNESCAPED_UNICODE);
        //Response($result);
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
            'name.required' => 'Tên khách hàng không được để trống',

        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required',

        ], $messages);

        $failed = $validator->fails();
        $isErr = false;

        if ($request->dms_code) {
            $dep_temp = Customer::where('dms_code', $request->dms_code)
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
                $fields = $request->all();
                $fields['user_id'] = auth()->user()->id;
                $re = Customer::create($fields);
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
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customers = Customer::findOrFail($id);

        $result = array();
        $result['data'] = array();
        $result['data'] = $customers;
        $result['success'] = "1";

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $result = array();
        $result['data'] = array();
        $result['data']['success'] = 0;
        date_default_timezone_set("Asia/Ho_Chi_Minh");

        $messages = [
            //'dms_code.required' => 'Mã DMS không được trùng',
            'name.required' => 'Tên khách hàng không được để trống',

        ];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ], $messages);
        $failed = $validator->fails();
        $isErr = false;

        if ($failed) {
            $result['data']['errors'] = $validator->errors();
        } else {
            $customers = Customer::find($id);
            $customers->dms_code = $request->input('dms_code');
            $customers->name = $request->input('name');
            $customers->address = $request->input('address');

            if ($customers->save()) {

                $result['data']['success'] = 1;

                $result['data']['data'] = $customers;

            }
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleteCustomer = Customer::findOrFail($id);
        $result = array();
        $result['data'] = array();
        $result['data']['success'] = 0;
        $validator = Validator::make(['id' => $id], []);
        $isErr = false;
        $check = Submission::where('customer_id', $id)->first();
        if ($check) {
            $validator->errors()->add('id', 'Không thể xóa khách hàng này, vì đã có tham gia chương trình. Vui lòng kiểm tra lại');
            $isErr = true;
        }
        if ($isErr) {
            $result['data']['errors'] = $validator->errors();
        } else {
            try {
                if ($deleteCustomer->delete()) {
                    $result['data']['success'] = 1;
                }
            } catch (\Exception $e) {
                $result['data']['errors'] = $e->getMessage();
            }
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
    public function customer_page(Request $request)
    {

        $result = array();
        $result['data'] = array();
        $result['success'] = 0;
        try {
            $user = User::find(auth()->user()->id);
            $perPage = 50; // Số lượng kết quả trên mỗi trang

            $query = Customer::query();
            if ($request->filled('query')) {
                $searchTerm = $request->input('query');
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('dms_code', 'like', "%$searchTerm%")
                        ->orWhere('name', 'like', "%$searchTerm%");
                });
            }
            if ($user->roles == 'GSBH') {
                $ids = User::where('supervisor_id', $user->id)->pluck('id');
                $query->whereIn('user_id', $ids)->orWhere('user_id', $user->id);
            } else if ($user->roles == 'QLGS') {
                // Do nothing, retrieve all customers
            } else {
                $query->where('user_id', auth()->user()->id);
            }

            $customers = $query->with('user')->paginate($perPage);

            $result['data'] = $customers->items();
            $result['paginate'] = [
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
                'total' => $customers->total(),
            ];
        } catch (Exception $e) {
            $result['data']['errors'] = $e->getMessage();
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
    public function lookup_customer(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $user = Auth()->user();
        $customers = Customer::query()->select('id','dms_code','name','address','user_id')->with([
            'user' => function ($query) {
                $query->select('id','name', 'staff_code');
            },
            'submissions' => function ($query) {
                $query->select('id','user_id','program_id', 'customer_id');
            },
            'submissions.program' => function ($query) {
                $query->select('id','name');
            },
            'submissions.submissionImages' => function ($query) {
                $query->select('id', 'submission_id', 'user_id', 'program_stage_id','is_approved','is_rejected','is_approved_level2','is_rejected_level2','send_date');
            },
            'submissions.submissionImages.program_stage' => function ($query) {
                $query->select('id', 'program_id', 'stage', 'status');
            },
            'submissions.submissionImages.program_stage.program' => function ($query) {
                $query->select('id', 'name');
            }
        ]);
        $customers = $customers->where(function ($customers) use ($user) {
            if ($user->roles === 'GSBH') {
                $customers->orWhere('user_id', $user->id)
                    ->orWhereHas('user', function ($customers) use ($user) {
                        $customers->where('supervisor_id', $user->id);
                    });
            } else if ($user->roles === 'QLGS') {

            } else {
                $customers->orWhere(function ($customers) use ($user) {
                    $customers->where('user_id', $user->id);
                });
            }
        });
        if($request->filled('query_dms_or_name')){
            $customers = $customers->where(function ($customers) use ($request) {
                $customers->orWhere('dms_code', 'like', "%{$request->query_dms_or_name}%")
                          ->orWhere('name', 'like', "%{$request->query_dms_or_name}%");
            });
        }
        $result = array();
        $result['data'] = array();
        $result['data']['success'] = 1;
        $list_customers = $customers->paginate($perPage, ['*'], 'page', $request->page);
        $result['data']['data'] = $list_customers->items();
        $result['paginate'] = [
            'current_page' => $list_customers->currentPage(),
            'last_page' => $list_customers->lastPage(),
            'total' => $list_customers->total(),
        ];

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    public function optimize_customer(Request $request)
    {
        $result = array();
        $result['data'] = array();
        $customers = Customer::query()->with(['user']);
        $user = Auth()->user();
        try {
            $customers = $customers->where(function ($customers) use ($user) {
                if ($user->roles === 'GSBH') {
                    $customers->orWhere('user_id', $user->id)
                        ->orWhereHas('user', function ($customers) use ($user) {
                            $customers->where('supervisor_id', $user->id);
                        });
                } else if ($user->roles === 'QLGS') {
    
                } else {
                    $customers->orWhere(function ($customers) use ($user) {
                        $customers->where('user_id', $user->id);
                    });
                }
            });
            if($request->filled('query_dms_or_name')){
                $customers = $customers->where(function ($customers) use ($request) {
                    $customers->orWhere('dms_code', 'like', "%{$request->query_dms_or_name}%")
                              ->orWhere('name', 'like', "%{$request->query_dms_or_name}%");
                });
            }
            $list_customers = $customers->paginate($request->perPage, ['*'], 'page', $request->page);
            $result['data']['data'] = $list_customers->items();
            $result['staff'] = ($user->supervisor_id !== null && $user->roles === null) ? true : false;
            $result['paginate'] = [
                'current_page' => $list_customers->currentPage(),
                'last_page' => $list_customers->lastPage(),
                'total' => $list_customers->total(),
            ];
    
        }  catch (Exception $e) {
            $result['data']['errors'] = $e->getMessage();
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);
    }

}
