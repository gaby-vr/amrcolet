<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Invoice;
use App\Traits\OrderInvoiceTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use PDF;

class FinanciarController extends Controller
{
    use OrderInvoiceTrait;

    public function index(Request $request)
    {
        return view('profile.dashboard', [
            'section' => 'financiar',
            'subsection' => null,
            'title' => __('Financiar')
        ]);
    }
}
