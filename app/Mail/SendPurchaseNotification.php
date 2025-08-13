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
  
class SendPurchaseNotification extends Mailable
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
            $subject = isset($details['subject']) ? $details['subject'] : __('Livrarea a fost confirmata');

            if($details['invoice_id'] != null) {
                $invoice = Invoice::firstWhere('id', $details['invoice_id']);

                $pdf = $this->getInvoicePDF($invoice);
            }

            $name = '';

            if($details['awb_api'] == 2) {
                $name = 'DPD';
                $courierGateway = app(CourierGateway::class, ['type' => $details['awb_api']]);
                $array['parcels'] = $courierGateway->getOrderParcels(['shipmentId' => $details['awb_shipment_id']]);
            } elseif($details['awb_api'] == 3) {
                $name = 'GLS';
                $courierGateway = app(CourierGateway::class, ['type' => $details['awb_api']]);
                $array['awb'] = $details['awb_shipment_id'];
                $array['return'] = true;
            } else {
                $name = 'Cargus';
                $courierGateway = app(CourierGateway::class, ['type' => $details['awb_api']]);
                $array = [
                    'barcode' => $details['awb_shipment_id'], 
                    'format' => 'A4',
                    'TotalWeight' => $details['total_weight'],
                    'createdAt' => $details['created_at']
                ];
            }
            $awb = $details['send_awb'] ? $courierGateway->getAWB($array) : false;

            $mail = $this->subject($subject);
            
            if($details['invoice_id'] != null) {
                $mail->attachData($pdf->output(),'Amrcolet '. $invoice->series. $invoice->number .' '. $invoice->payed_on .'.pdf');
            }
            if($awb !== false) {
                $mail->attachData($awb, $name.'_AWB_'.$details['awb_shipment_id'].'.pdf');
            }

            return $mail->view('mail.purchase')->with([
                'details' => $details,
            ]);

        } else {
            $subject = __('Livrarea nu a putut fi finalizata');
            return $this->subject($subject)
                    ->view('mail.purchase');
        }
    }
}