<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Invoice;
use App\Traits\OrderInvoiceTrait;
use App\Exports\ExportInvoices;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use PDF;
use Excel;

class InvoicesController extends Controller
{
    use OrderInvoiceTrait;

    public function index(Request $request)
    {
        $invoices = Invoice::where('id', '<>', 0)->where('status', 1);
        if($request->input()) {
            if($request->input('from') != "")
            {
                $invoices->whereDate('invoices.payed_on', '>=', $request->input('from'));
            }
            if($request->input('to') != "")
            {
                $invoices->whereDate('invoices.payed_on', '<=', $request->input('to'));
            }
            if($request->has('email') && $request->input('email') != "")
            {
                $invoices->whereHas('user', function($query) use($request) {
                    $query->where('email', 'like', $request->input('email').'%');
                });
            }
            if($request->input('status') != "")
            {
                $invoices->where('invoices.status', '=', $request->input('status'));
            }
        }
        $invoices = $invoices->orderByDesc('updated_at')->paginate(10);
        return view('admin.invoices.show', [
            'invoices' => $invoices,
            'condtitions' => $request->input(),
        ]);
    }

    // public function showPDF(Invoice $invoice, $stream = TRUE)
    // {   
    //     if($invoice->status != 1)
    //     {
    //         return redirect()->route('home');
    //     }
    //     $data = [
    //         'factura' => $invoice,
    //     ];
    //     $pdf = PDF::loadView('invoice.invoice', $data);
    //     if ($stream) 
    //     {
    //         return $pdf->stream('Factura ' . setare('PROVIDER_NAME') .' '. $invoice->series. $invoice->number .' '. $invoice->payed_on . '.pdf')->header('Content-Type','application/pdf');
    //     } 
    //     else 
    //     {
    //         return $pdf->download('Factura ' . setare('PROVIDER_NAME') .' '. $invoice->series. $invoice->number .' '. $invoice->payed_on . '.pdf');
    //     }
    // }

    public function downloadExcel()
    {
        return Excel::download(new ExportInvoices, config('app.name').'_facturi_'.date('Y-m-d').'.xlsx');
    }
}