<?php

namespace App\Console\Commands;

use App\Models\Livrare;
use App\Models\LivrareStatus;
use App\Models\User;
use App\Traits\InvoiceSheetCreationTrait;
use Illuminate\Console\Command;

class AddToInvoiceSheet extends Command
{
    use InvoiceSheetCreationTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:invoice-sheet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check orders and add them to a invoice sheet.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $contractors = User::with(['lastInvoiceSheet','lastInvoiceSheet.sheetAwbs'])
            ->where('role', '2')
            ->whereHas('orders', function ($query) {
                $query->where('updated_at', '>=', now()->subMonths(1));
            })->get();

        foreach ($contractors as $user) {

            $this->updateInvoiceSheet($user->lastInvoiceSheet, $user, true);   
        }
    }

    
}