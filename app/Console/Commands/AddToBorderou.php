<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Courier\CourierGateway;
use App\Models\Borderou;
use App\Models\BorderouLivrare;
use App\Models\Repayment;
use App\Models\Livrare;
use App\Models\LivrareStatus;
use App\Models\User;
use App\Traits\BorderouCreationTrait;
use Log;
use Mail;
use DateTime;

class AddToBorderou extends Command
{
    use BorderouCreationTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:borderou';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check repayments and add them to a borderou list.';

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
        $contractors = User::with(['lastBorderou','lastBorderou.borderouAwbs'])
            /*->where('role', '2')*/
            ->whereHas('orders', function ($query) {
                $query->where('status', '1')
                    ->whereNotNull('ramburs_value')
                    ->where('created_at', '>=', now()->subMonths(1));
            })->get();

        foreach ($contractors as $user) {

            $this->updateBorderou($user->lastBorderou, $user);   
        }
    }

    
}