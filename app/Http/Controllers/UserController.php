<?php

namespace App\Http\Controllers;

// use App\Submission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController\BaseController;
use App\User;

class UserController extends BaseController
{
    public function index(){
        $query = User::query();
        $result = array();
        $result['data'] = array();

        try {
            $query = $query->where('roles','GSBH');
            $user = $query->get();
            $result['data'] = $user;
            $result['success'] = "1";
        } catch (Exception $e) {
            $result['data']['errors'] = $e->getMessage();
        }

        return json_encode($result, JSON_UNESCAPED_UNICODE); //Response($result);
    }
}
