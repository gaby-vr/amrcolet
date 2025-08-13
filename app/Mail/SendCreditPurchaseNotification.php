<?php
  
namespace App\Mail;
  
use App\Courier\CourierGateway;
use App\Traits\OrderInvoiceTrait;
use App\Models\Invoice;
use App\Models\InvoiceMeta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
  
class SendCreditPurchaseNotification extends Mailable
{
    use Queueable, SerializesModels, OrderInvoiceTrait;
  
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
        if($details['action'] == 1) {
            $subject = isset($details['subject']) ? $details['subject'] : __('Contul a fost reincarcat cu credite');

            if($details['invoice_id'] != null) {
                $invoice = Invoice::firstWhere('id', $details['invoice_id']);

                $pdf = $this->getInvoicePDF($invoice);
            }

            
            if($details['invoice_id'] != null) {
                return $this->subject($subject)
                    ->attachData($pdf->output(),'Amrcolet '. $invoice->series. $invoice->number .' '. $invoice->payed_on .'.pdf')
                    ->view('mail.purchase')
                    ->with([
                        'details' => $details,
                      ]);
            } else {
                return $this->subject($subject)
                    ->view('mail.purchase')
                    ->with([
                        'details' => $details,
                      ]);
            }

        } else {
            $subject = __('Contul nu a putut fi reincarcat cu credite');
            return $this->subject($subject)
                    ->view('mail.purchase');
        }
    }
}