<?php

namespace App\Http\Controllers\BaseController;

use App\Http\Controllers\Controller;
use App\login;
use App\User;

use Illuminate\Support\Facades\Auth;
class BaseController extends Controller
{
    public function __construct()
    {

        // $this->middleware(function ($request, $next) {
        //     $access_token = "";

        //     $user = Auth()->user();
        //     view()->share(compact('access_token'));

        //     return $next($request);
        // });
         $this->middleware('auth');

        $this->middleware(function ($request, $next) {
            //load menu từ Role_user
            $auth_user = Auth()->user();

            $access_token = "";
            // Nếu không phải login từ API thì tạo token cho user
            if (!$auth_user->token()) {
                if ($request->session() && $request->session()->get('user')) {
                    $access_token = $request->session()->get('user');
                } else {
                    $authToken =  $auth_user->createToken('authToken');
                    $access_token = $authToken->accessToken; // $auth_user->createToken('authToken')->accessToken;
                    $auth_user->withAccessToken($access_token);
                    $request->session()->put('user', $access_token);

                }

            }
            view()->share(compact( 'access_token'));
            return $next($request);
        });
    }
    public function sendResponse($result, $message = '', $code = 200)
    {

        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }
    public function sendError($error, $errorMessage = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error
        ];
        if (!empty($errorMessage)) {
            $response['errors'] = $errorMessage;
        }
        return response()->json($response, $code);
    }

    public function sendOk()
    {
        return response();
    }
    public function sendSuccess($message)
    {
        $response = [
            'success' => true,
            'message' => $message
        ];
        return response()->json($response, 200);
    }
    public function sendFailedWithMessage($message)
    {
        $response = [
            'success' => false,
            'message' => $message
        ];
        return response()->json($response, 200);
    }
    public function sendFailedWithStatusCode($message, $code)
    {
        $response = [
            'success' => false,
            'message' => $message
        ];
        return response()->json($response, $code);
    }
}
