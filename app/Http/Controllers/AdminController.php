<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function loginadm()
    {
        return view('admin.loginadm');
    }
    public function homepageadm()
    {
        return view('admin.homepageadm');
    }
    public function moviedetailsadm()
    {
        return view('admin.moviedetailsadm');
    }
    public function addmovie()
    {
        return view('admin.addmovie');
    }



}
