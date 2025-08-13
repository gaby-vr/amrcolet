<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use App\Models\User;
use App\Models\Repayment;
use Carbon\Carbon;
use DateTime;
use DB;

class RepaymentsController extends Controller
{

    public function index(Request $request)
    {
        $repayments = Repayment::where('awb', '<>', '0');
        if($request->input()) {
            if($request->input('from') != "")
            {
                $repayments->whereDate('repayments.date_order', '>=', $request->input('from'));
            }
            if($request->input('to') != "")
            {
                $repayments->whereDate('repayments.date_order', '<=', $request->input('to'));
            }
            if($request->has('status') && $request->input('status') != "")
            {
                switch ($request->input('status')) {
                    case '1':
                        $repayments->whereNotNull('repayments.date_delivered')
                            ->where('repayments.status', '1')
                            ->whereIn('repayments.type', ['3','2']);
                        break;
                    case '2':
                        $repayments->whereNotNull('repayments.date_delivered')
                            ->where('repayments.status', '1')
                            ->where('repayments.type', '1');
                        break;
                    case '3':
                        $repayments->whereNotNull('repayments.date_delivered')
                            ->where('repayments.status', '1')
                            ->where('repayments.type', '2');
                        break;
                    case '4':
                        $repayments->whereNull('repayments.date_delivered')
                            ->where('repayments.status', '0');
                        break;
                    default:
                        break;
                }
            }
        }
        $repayments = $repayments->orderByDesc('created_at')->paginate(20);
        $repayments->appends($request->query());
        return view('admin.repayments.show', [
            'repayments' => $repayments,
            'condtitions' => $request->input(),
        ]);
    }

    public function complete(Request $request, Repayment $repayment)
    {
        $repayment->type = '3';
        $repayment->save();
        session()->flash('success', 'Rambursul a fost actualizat.');
        return redirect()->route('admin.repayments.show');
    }
}