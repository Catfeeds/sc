<?php

namespace App\Http\Controllers;

class AdminController extends BaseController
{
    public function __construct()
    {
    }

    public function login()
    {
        return view('admin.login');
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }
}
