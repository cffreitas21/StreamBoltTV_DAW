<?php

namespace App\Helpers;

class AnalyticsHelper
{
    protected static $filePath;

    protected static function getFilePath()
    {
        if (!self::$filePath) {
            self::$filePath = storage_path('app/analytics.json');
        }
        return self::$filePath;
    }

    protected static function readData()
    {
        $file = self::getFilePath();
        
        if (!file_exists($file)) {
            file_put_contents($file, json_encode([]));
            return [];
        }
        
        return json_decode(file_get_contents($file), true) ?? [];
    }

    protected static function writeData($data)
    {
        file_put_contents(self::getFilePath(), json_encode($data, JSON_PRETTY_PRINT));
    }

    public static function trackLogin($userId, $userName)
    {
        $data = self::readData();
        
        $data[] = [
            'user_id' => $userId,
            'name' => $userName,
            'type' => 'login',
            'timestamp' => now()->toDateTimeString(),
        ];
        
        self::writeData($data);
    }

    public static function trackSearch($userId, $userName, $query = null)
    {
        $data = self::readData();
        
        $data[] = [
            'user_id' => $userId,
            'name' => $userName,
            'type' => 'search',
            'query' => $query,
            'timestamp' => now()->toDateTimeString(),
        ];
        
        self::writeData($data);
    }

    public static function trackComment($userId, $userName)
    {
        $data = self::readData();
        
        $data[] = [
            'user_id' => $userId,
            'name' => $userName,
            'type' => 'comment',
            'timestamp' => now()->toDateTimeString(),
        ];
        
        self::writeData($data);
    }

    public static function trackTime($userId, $userName, $duration)
    {
        $data = self::readData();
        
        $data[] = [
            'user_id' => $userId,
            'name' => $userName,
            'type' => 'time',
            'duration' => $duration,
            'timestamp' => now()->toDateTimeString(),
        ];
        
        self::writeData($data);
    }
}
