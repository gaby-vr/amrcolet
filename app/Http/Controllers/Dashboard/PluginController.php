<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PluginController extends Controller
{
    public function index(Request $request)
    {
        return view('profile.dashboard', [
            'section' => 'plugin',
            'subsection' => null,
            'title' => __('Wordpress plugin')
        ]);
    }

    public function downloadWordpressPlugin(Request $request)
    {
        if(auth()->user()->meta('wordpress_active') == '1') {
            $path = storage_path().'/app/files/amrcolet.ro.zip';
            if (file_exists($path)) {
                return response()->download($path);
            }
        }
        return redirect()->route('dashboard.wordpress');
    }
}
