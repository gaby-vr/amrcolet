<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Livrare extends Model
{
    protected $fillable = [
        'user_id',
        'invoice_id',
        'email',   
        'curier',
        'api',
        'api_shipment_id',
        'api_shipment_awb',
        'status',
        'type', 
        'original_value',  
        'value',   
        'nr_colete',   
        'total_weight',   
        'total_volume',
        'content', 
        'awb',
        'delivered_on', 
        'pickup_day',  
        'start_pickup_hour',   
        'end_pickup_hour',
        'work_saturday',
        'open_when_received',
        'retur_document',
        'swap_details',
        'send_sms',
        'ramburs',
        'ramburs_value',
        'ramburs_currency',
        'titular_cont',
        'iban',
        'customer_reference',  
        'assurance',
        'nr_credits_used',
        'voucher_code',    
        'voucher_type',    
        'voucher_value',
        'payed',
        'manual_status',
        'created_at'
    ];

    protected $table = 'livrari';

    protected $casts = [
        'swap_details' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creditedInvoice()
    {
        return $this->invoice->storno();
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function sender()
    {
        return $this->hasOne(Contact::class)->where('type','1');
    }

    public function receiver()
    {
        return $this->hasOne(Contact::class)->where('type','2');
    }

    public function packages()
    {
        return $this->hasMany(Package::class)->where('template_id','0');
    }

    public function curier_model()
    {
        return $this->hasOne(Curier::class, 'name', 'curier');
    }

    public function repayment()
    {
        return $this->hasOne(Repayment::class, 'awb', 'api_shipment_awb');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function awbLabels()
    {
        return $this->hasOne(LivrareAwb::class, 'api_awb', 'api_shipment_awb');
    }

    public function cancelRequest()
    {
        return $this->hasOne(LivrareCancelRequest::class, 'livrare_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Local scopes
    |--------------------------------------------------------------------------
    */

    public function scopeJoinAwbLables($query)
    {
        if (is_null($query->getQuery()->columns)) {
            $query->select($this->getTable().'.*', $this->getTable().'.id');
        }
        $label_t = (new LivrareAwb)->getTable();
        $label_f = array_diff((new LivrareAwb)->getFillable(), $this->getFillable());
        $query->addSelect(...$label_f)->leftJoin($label_t, 'api_awb', 'api_shipment_awb');
    }

    public function scopeManualStatus($query)
    {
        $query->whereNotNull('manual_status');
    }

    public function scopeAutoStatus($query)
    {
        $query->whereNull('manual_status');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors and Mutators
    |--------------------------------------------------------------------------
    */

    public function getAmountAttribute()
    {
        return (float)$this->value - (float)$this->nr_credits_used;
    }

    public function getStatusTextAttribute()
    {
        return static::statusList($this->status);
    }

    public function getStatusColorAttribute()
    {
        if($this->status === '0') {
            return 'yellow';
        } elseif($this->status === '1') {
            return 'green';
        } elseif($this->status === '5') {
            return 'red';
        } elseif($this->status === '8') {
            return 'purple';
        } elseif( in_array($this->status, ['6','7']) || $this->status >= 9) {
            return 'orange';
        }
        return 'blue';
    }

    /*
    |--------------------------------------------------------------------------
    | Custom functions
    |--------------------------------------------------------------------------
    */

    public static function statusList($status = null)
    {
        $list = [
            0 => __('Neridicata'),
            1 => __('Livrata'),
            2 => __('In tranzit'),
            3 => __('Ajuns in depozit destinatie'),
            4 => __('In livrare'),
            5 => __('Anulata'),
            6 => __('Propus spre anulare'),
            7 => __('Ridicare din sediu'),
            8 => __('Returnata'),
            9 => __('Livrare reprogramata'),
            10 => __('Redirectionare'),
            11 => __('Adresa gresita/incompleta'),
            12 => __('Refuzata'),
            13 => __('Livrare nereusita'),
            14 => __('Expediere semnalata cu avarie'),
            15 => __('Expediere incompleta'), // only for DPD
            16 => __('Comanda in asteptare'),
        ];
        return $status !== null ? ($list[$status] ?? '') : $list;
    }
}
