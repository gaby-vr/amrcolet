<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Borderou;
use App\Traits\ConversionTrait;
use App\Traits\BorderouCreationTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;

class BorderouriController extends Controller
{
    use ConversionTrait, BorderouCreationTrait;

    public function index(Request $request)
    {
        return view('profile.borderouri.index', [
            'title' => __('Lista borderouri'),
            'items' => $this->search($request),
            'condtitions' => $request->input(),
        ]);
    }

    public function search(Request $request)
    {
        $table = Borderou::getTableName();
        $items = auth()->user()->borderouri()->notExcluded()->payed();
        if($request->input()) {
            if($request->input('from') != "") {
                $items->whereDate($table.'.start_date', '>=', $this->transformDate($request->input('from'), 'Y-m-d', 'php', 'd/m/Y'));
            }
            if($request->input('to') != "") {
                $items->whereDate($table.'.end_date', '<=', $this->transformDate($request->input('to'), 'Y-m-d', 'php', 'd/m/Y'));
            }
            if($request->input('payed_from') != "") {
                $items->whereDate($table.'.payed_at', '>=', $this->transformDate($request->input('payed_from'), 'Y-m-d', 'php', 'd/m/Y'));
            }
            if($request->input('payed_to') != "") {
                $items->whereDate($table.'.payed_at', '<=', $this->transformDate($request->input('payed_to'), 'Y-m-d', 'php', 'd/m/Y'));
            }
        }
        return $items->orderByDesc('created_at')->paginate(15)->appends($request->query());
    }
}
