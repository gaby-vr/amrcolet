<?php
  
namespace App\Mail;
  
use App\Exports\ExportInvoiceSheet;
use App\Traits\InvoiceSheetCreationTrait;
use App\Traits\OrderInvoiceTrait;
use App\Models\Invoice;
use App\Models\InvoiceMeta;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
  
class SendInvoiceSheetPayedNotification extends Mailable
{
    use Queueable, SerializesModels, InvoiceSheetCreationTrait, OrderInvoiceTrait;
  
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
        $invoice_sheet = $details['invoice_sheet'];
        $invoice_sheet_data = [
            'id' => $invoice_sheet->id,
            'payed_at' => $invoice_sheet->transformDate('payed_at', 'd.m.Y', from_format: 'Y-m-d'),
        ];
        $subject = __('Factura amrcolet.ro din :payed_at', [
            'payed_at' => $invoice_sheet_data['payed_at']
        ]);

        $body = __('Stimate client,<br>
        Va trimitem atasat factura pentru serviciile de curierat prestate de AMR COLET SRL, precum si borderoul aferent acestei facturi.<br>
        Pentru orice nelamurire sau pentru informatii suplimentare privitoare la factura sau borderoul atasat acestui email va rugam nu ezitati sa ne contactati.<br>
        <br>

        Cu respect, <br>
        AMR COLET SRL<br>
        Tel: 0727545441 <br> 
        www.amrcolet.ro', $invoice_sheet_data);

        $data = ['details' => ['title' => $subject, 'body' => $body]];
        $this->details += ['title' => $subject, 'body' => $body];

        if($invoice = $invoice_sheet->invoice) {
            $pdf = $this->getInvoicePDF($invoice);
        }

        return $this->subject($subject)
            ->attachData($this->attachExcel(request(), $invoice_sheet), __('Fisa facturi #:id :payed_at.xlsx', $invoice_sheet_data))
            ->attachData($pdf->output(),'Amrcolet '. $invoice->series. $invoice->number .' '. $invoice->payed_on .'.pdf')
            ->view('mail.purchase')
            ->with($data);

    }
}