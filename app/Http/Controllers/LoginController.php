<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\login;

class LoginController extends Controller
{
    public function username()
    {
        $login = request()->input('email');
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'staff_code';
        request()->merge([$field => $login]);
        return $field;
    }
    public function login(Request $request)
    {
        //TODO: sẽ khoá hàm này trong tương lai
        //dd("Sẽ khoá");->sẽ khoá sau khi onethienlong chạy one -> dự trù cho app mobile duyệt cloud
        $login = $request->validate([
            'staff_code' => 'required',
            'password' => 'required',
        ]);
        $login['active'] = 1;//check user đang active
        if (!Auth::attempt($login)) {
            return Response(['message' => 'Invalid login creadentials', 'success' => '0']);
        }
        $user = new User();
        $user = Auth::user();
        /// dd($user);
        $accessToken = $user->createToken('authToken')->accessToken;;
        return Response(['user' => Auth::user(), 'access_token' => $accessToken, 'success' => '1']);
    }
}
