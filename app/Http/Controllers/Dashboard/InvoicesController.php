<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Invoice;
use App\Traits\OrderInvoiceTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;

class InvoicesController extends Controller
{
    use OrderInvoiceTrait;

    public function index(Request $request)
    {
        return view('profile.dashboard', [
            'section' => 'invoices',
            'subsection' => null,
            'title' => __('Lista facturi')
        ]);
    }
}
