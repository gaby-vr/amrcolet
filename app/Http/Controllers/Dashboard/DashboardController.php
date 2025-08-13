<?php

namespace App\Http\Controllers\Dashboard;

use App\Traits\OrderValidationTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    use OrderValidationTrait;

    public function index(Request $request)
    {
        return redirect()->route('dashboard.invoice.show');
    }

    public function announcement(Request $request)
    {
        auth()->user()->setMeta('announcement_seen', 1);
        return view('announcement');
    }
}
