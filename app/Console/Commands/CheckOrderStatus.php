<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Courier\CourierGateway;
use App\Models\Repayment;
use App\Models\Livrare;
use App\Models\LivrareStatus;
use App\Models\LivrareCancelRequest;
use App\Traits\OrderStatusCheckTrait;
use Log;
use Mail;
use DateTime;

class CheckOrderStatus extends Command
{
    use OrderStatusCheckTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the status of the all unfinished orders.';

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
        try {
            get_cursuri_valutare_array();
        } catch(\Throwable $e) {}
        $livrari = Livrare::autoStatus()->where(function($query) {
                $query->whereNotIn('status', ['1','-1','8', '5']);
                // only the last cancelled orders by Cargus in the last 3 days 
                // ->orWhere(function($subquery) {
                //     $subquery->whereIn('status', ['5', '8'])
                //         ->where('api', '<>', 3)
                //         ->where('updated_at', '>', now()->subDays(3));
                // });
            })->whereNotNull('api_shipment_id')
            ->whereNotNull('api_shipment_awb')
            ->whereDate('updated_at', '>=', now()->subMonths(1))
            ->get();
        foreach ($livrari as $livrare) {
            switch ($livrare->api) {
                case 1:
                    self::checkUrgentCargusOrder($livrare);
                    break;
                case 2:
                    self::checkDPDOrder($livrare);
                    break;
                case 3:
                    self::checkGLSOrder($livrare);
                default:
                    break;
            }
        }
    }
}