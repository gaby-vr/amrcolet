<?php

namespace App\Console\Commands;

use App\Courier\CourierGateway;
use App\Models\Borderou;
use App\Models\Repayment;
use App\Traits\BorderouCreationTrait;
use Illuminate\Console\Command;

class CheckBorderouPaymentStatus extends Command
{
    use BorderouCreationTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:borderou';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the status of the unpayed borderou.';

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
        $borderouri = Borderou::whereNull('payed_at')
            ->whereDate('end_date', '>=', now()->subMonths(1))
            ->whereDate('end_date', '<=', now())
            ->whereHas('borderouAwbs')->whereHas('borderouApiRequests', function ($query) {
                $query->whereNotNull('payment_id');
            })->with(['borderouApiRequests' => function ($query) {
                $query->whereNotNull('payment_id');
            }])->get();
        foreach ($borderouri as $borderou) {
            $this->checkStatusApiRequestsBorderou($borderou);
        }
    }
}