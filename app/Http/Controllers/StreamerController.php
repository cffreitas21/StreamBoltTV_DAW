<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\AnalyticsHelper;

class StreamerController extends Controller
{
    public function loginstreamer()
    {
        return view('streamer.loginstreamer');
    }
    
    public function homepage(Request $request)
    {
        // Track search if query parameter exists
        if ($request->has('query') && $request->query('query') !== '' && auth()->check()) {
            AnalyticsHelper::trackSearch(
                auth()->id(),
                auth()->user()->name,
                $request->query('query')
            );
        }
        
        return view('streamer.homepage');
    }

    public function moviedetails()
    {
        return view('streamer.moviedetails');
    }

    public function trackTime(Request $request)
    {
        if (auth()->check() && auth()->user()->isStreamer()) {
            $duration = $request->input('duration', 0);
            
            AnalyticsHelper::trackTime(
                auth()->id(),
                auth()->user()->name,
                $duration
            );
            
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 403);
    }



}
