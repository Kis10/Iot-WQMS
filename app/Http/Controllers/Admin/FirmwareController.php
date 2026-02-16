<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FirmwareController extends Controller
{
    private $firmwarePath;

    public function __construct()
    {
        $this->firmwarePath = base_path('firmware/aquasense.ino');
    }

    public function wifi()
    {
        $content = File::get($this->firmwarePath);
        
        // Extract current details using regex
        preg_match('/const char\* ssid\s*=\s*"([^"]*)";/', $content, $ssidMatches);
        preg_match('/const char\* password\s*=\s*"([^"]*)";/', $content, $passwordMatches);

        $ssid = $ssidMatches[1] ?? '';
        $password = $passwordMatches[1] ?? '';

        return view('admin.firmware.wifi', compact('ssid', 'password'));
    }

    public function updateWifi(Request $request)
    {
        $request->validate([
            'ssid' => 'required|string',
            'password' => 'required|string',
        ]);

        $content = File::get($this->firmwarePath);
        
        $content = preg_replace(
            '/const char\* ssid\s*=\s*"[^"]*";/', 
            'const char* ssid     = "' . $request->ssid . '";', 
            $content
        );
        
        $content = preg_replace(
            '/const char\* password\s*=\s*"[^"]*";/', 
            'const char* password = "' . $request->password . '";', 
            $content
        );

        File::put($this->firmwarePath, $content);

        return back()->with('success', 'WiFi Credentials updated in firmware!');
    }

    public function api()
    {
        $content = File::get($this->firmwarePath);

        preg_match('/const char\* serverName\s*=\s*"([^"]*)";/', $content, $serverMatches);
        preg_match('/const char\* deviceToken\s*=\s*"([^"]*)";/', $content, $tokenMatches);
        preg_match('/const char\* deviceID\s*=\s*"([^"]*)";/', $content, $idMatches);

        $serverName = $serverMatches[1] ?? '';
        $deviceToken = $tokenMatches[1] ?? '';
        $deviceID = $idMatches[1] ?? '';

        return view('admin.firmware.api', compact('serverName', 'deviceToken', 'deviceID'));
    }

    public function updateApi(Request $request)
    {
        $request->validate([
            'server_name' => 'required|url',
            'device_token' => 'required|string',
            'device_id' => 'required|string',
        ]);

        $content = File::get($this->firmwarePath);

        $content = preg_replace(
            '/const char\* serverName\s*=\s*"[^"]*";/', 
            'const char* serverName = "' . $request->server_name . '";', 
            $content
        );
        
        $content = preg_replace(
            '/const char\* deviceToken\s*=\s*"[^"]*";/', 
            'const char* deviceToken = "' . $request->device_token . '";', 
            $content
        );

        $content = preg_replace(
            '/const char\* deviceID\s*=\s*"[^"]*";/', 
            'const char* deviceID    = "' . $request->device_id . '";', 
            $content
        );

        File::put($this->firmwarePath, $content);

        return back()->with('success', 'API Configuration updated in firmware!');
    }
}
