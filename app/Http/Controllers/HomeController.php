<?php

namespace App\Http\Controllers;


use App\Http\Controllers\BaseController\BaseController;
class HomeController extends BaseController
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    public function dmsSale()
    {
        return view('dms');
    }
    public function dms_approved()
    {
        return view('dms_approved');
    }
    public function submission_register()
    {
        return view('dms_register');
    }

    public function customer(){
        return view('dms_customer');
    }

    public function program_admin(){
        return view('dms_program_admin');
    }
    public function program(){
        return view('dms_program');
    }
    public function suppervision(){
        return view('dms_suppervision');
    }
    public function program_stages(){
        return view('dms_program_stages');
    }
    public function lookup_customer(){
        return view('dms_lookup_customer');
    }
}
