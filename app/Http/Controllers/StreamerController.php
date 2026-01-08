<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StreamerController extends Controller
{
    public function loginstreamer()
    {
        return view('streamer.loginstreamer');
    }
    public function homepage()
    {
        return view('streamer.homepage');
    }

    public function moviedetails()
    {
        return view('streamer.moviedetails');
    }



}
