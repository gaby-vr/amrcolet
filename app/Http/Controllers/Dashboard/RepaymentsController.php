<?php

namespace App\Http\Controllers\Dashboard;

use App\Billing\PaymentGateway;
use App\Exports\ExportRepayments;
use App\Models\Repayment;
use App\Models\User;
use App\Traits\OrderInvoiceTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Excel;
use PDF;

class RepaymentsController extends Controller
{
    use OrderInvoiceTrait;

    public function index(Request $request)
    {
        return view('profile.dashboard', [
            'section' => 'repayments',
            'subsection' => null,
            'title' => __('Situatie rambursuri')
        ]);
    }

    public function emailExcel(Repayment $repayment)
    {   
        if($repayment->user_id != auth()->id()) {
            return redirect()->route('home');
        }
        $data = [
            'factura' => $repayment,
        ];
        $pdf = PDF::loadView('repayment.repayment', $data);
        if ($stream) 
        {
            return $pdf->stream('Factura ' .' '. $repayment->series. $repayment->number .' '. $repayment->payed_on . '.pdf')->header('Content-Type','application/pdf');
        } 
        else 
        {
            return $pdf->download('Factura ' .' '. $repayment->series. $repayment->number .' '. $repayment->payed_on . '.pdf');
        }
    }

    public function downloadExcel(Repayment $repayment)
    {   
        return Excel::download(new ExportRepayments, 'repayments.xlsx');
    }
}
