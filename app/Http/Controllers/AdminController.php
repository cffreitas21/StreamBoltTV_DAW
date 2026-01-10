<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Retorna a vista de login do admin
    public function loginadm()
    {
        return view('admin.loginadm');
    }

    // Retorna a vista homepage do admin
    public function homepageadm()
    {
        return view('admin.homepageadm');
    }

    // Retorna a vista de detalhes do filme para admin
    public function moviedetailsadm()
    {
        return view('admin.moviedetailsadm');
    }

    // Retorna a vista para adicionar filme
    public function addmovie()
    {
        return view('admin.addmovie');
    }

    // Processa e exibe estatísticas de utilizadores streamers
    public function analytics()
    {
        // Read analytics data from JSON file
        $analyticsFile = storage_path('app/analytics.json');
        
        if (!file_exists($analyticsFile)) {
            file_put_contents($analyticsFile, json_encode([]));
        }
        
        $analyticsData = json_decode(file_get_contents($analyticsFile), true);
        
        // Process and aggregate data by user
        $streamers = [];
        
        foreach ($analyticsData as $entry) {
            $userId = $entry['user_id'] ?? 0;
            $userName = $entry['name'] ?? 'Unknown';
            
            if (!isset($streamers[$userId])) {
                $streamers[$userId] = [
                    'name' => $userName,
                    'logins' => 0,
                    'total_time' => 0,
                    'searches' => 0,
                    'comments' => 0,
                    'last_activity' => null,
                    'session_count' => 0,
                ];
            }
            
            // Update name if it changed
            $streamers[$userId]['name'] = $userName;
            
            $type = $entry['type'] ?? '';
            
            switch ($type) {
                case 'login':
                    $streamers[$userId]['logins']++;
                    $streamers[$userId]['session_count']++;
                    break;
                case 'search':
                    $streamers[$userId]['searches']++;
                    break;
                case 'comment':
                    $streamers[$userId]['comments']++;
                    break;
                case 'time':
                    $streamers[$userId]['total_time'] += intval($entry['duration'] ?? 0);
                    break;
            }
            
            // Update last activity
            if (isset($entry['timestamp'])) {
                if ($streamers[$userId]['last_activity'] === null) {
                    $streamers[$userId]['last_activity'] = $entry['timestamp'];
                } else {
                    $currentLast = strtotime($streamers[$userId]['last_activity']);
                    $newTime = strtotime($entry['timestamp']);
                    if ($newTime > $currentLast) {
                        $streamers[$userId]['last_activity'] = $entry['timestamp'];
                    }
                }
            }
        }
        
        // Set default timestamp for users without activity
        foreach ($streamers as $userId => $data) {
            if ($data['last_activity'] === null) {
                $streamers[$userId]['last_activity'] = now()->toDateTimeString();
            }
            
            // Calcular tempo médio por sessão
            $sessionCount = $data['session_count'] > 0 ? $data['session_count'] : 1;
            $streamers[$userId]['avg_time_per_session'] = $data['total_time'] / $sessionCount;
        }
        
        return view('admin.analytics', compact('streamers'));
    }



}
