<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use App\Models\UserMeta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Validator;

class AnnouncementController extends Controller
{
    public function edit()
    {
        return view('admin.announcement');
    }

    public function update()
    {

        $input = Validator::make(request()->input(), [
            'announcement' => ['required', 'string'],
        ])->validate();

        Setting::where('name', 'ANNOUNCEMENT')->update([
            'value' => $input['announcement'],
        ]);

        auth()->user()->unsetMeta('announcement_seen');

        session()->flash('success', 'Anuntul a fost modificata cu succes.');

        return redirect()->route('admin.announcement.edit');
    }
}