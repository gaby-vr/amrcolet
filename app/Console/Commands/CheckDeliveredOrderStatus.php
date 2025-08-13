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

class CheckDeliveredOrderStatus extends Command
{
    use OrderStatusCheckTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:delivered-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the status of the delivered orders.';

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
        $livrari = Livrare::autoStatus()->whereIn('status', ['5', '8', '1'])
        ->where('updated_at', '>=', now()->subDays(3))
        ->whereNotNull('api_shipment_id')
        ->whereNotNull('api_shipment_awb')
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
                    break;
                default:
                    break;
            }
        }
    }

    
}