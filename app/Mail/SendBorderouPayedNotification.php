<?php
  
namespace App\Mail;
  
use App\Exports\ExportBorderouri;
use App\Traits\BorderouCreationTrait;
use App\Models\Invoice;
use App\Models\InvoiceMeta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
  
class SendBorderouPayedNotification extends Mailable
{
    use Queueable, SerializesModels, BorderouCreationTrait;
  
    public $details;
  
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }
  
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        ini_set('memory_limit', '256M');
        $details = $this->details;
        $borderou = $details['borderou'];
        $borderou_data = [
            'id' => $borderou->id,
            'payed_at' => $borderou->transformDate('payed_at', 'd.m.Y', from_format: 'Y-m-d'),
        ];
        $subject = __('Borderou rambursuri amrcolet.ro din :payed_at', [
            'payed_at' => $borderou_data['payed_at']
        ]);

        $body = __('Buna ziua, <br>
        In atasament regasiti borderoul expeditiilor #:id pentru care s-a facut viramentul bancar in data de :payed_at <br>
        Multumim <br><br>
        
        Cu stima, <br>
        AMR COLET <br>
        Tel: 0727545441 <br> 
        www.amrcolet.ro', $borderou_data);

        $data = ['details' => ['title' => $subject, 'body' => $body]];
        $this->details += ['title' => $subject, 'body' => $body];

        if($borderou != null) {
            return $this->subject($subject)
                ->attachData($this->attachExcel(request(), $borderou), __('Borderou #:id :payed_at.xlsx', $borderou_data))
                ->view('mail.purchase')
                ->with($data);
        } else {
            return $this->subject($subject)
                ->view('mail.purchase', $data)
                ->with($data);
        }
    }
}